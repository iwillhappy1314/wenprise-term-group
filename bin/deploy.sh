#!/bin/bash

# args
MSG=${1-'deploy from git'}
MAINFILE="wenprise-term-group.php"
WP_ORG_USERNAME="iwillhappy1314"

#####################################################
# 定义 Git 和 SVN 仓库
#####################################################
SVN_REPO="https://plugins.svn.wordpress.org/wenprise-term-group/"
GH_REF=https://github.com/${TRAVIS_REPO_SLUG}.git

# paths
#SRC_DIR=$(git rev-parse --show-toplevel)
#DIR_NAME=$(basename $SRC_DIR)
#DEST_DIR=~/Plugins/$DIR_NAME
#TRUNK="$DEST_DIR/trunk"

# 检查 readme.txt 中的版本和插件主文件中的版本
#READMEVERSION=`grep "Stable tag" $SRC_DIR/readme.txt | awk '{ print $NF}'`
#PLUGINVERSION=`grep "Version:" $SRC_DIR/$MAINFILE | awk '{ print $NF}'`

# pull request 时不部署
if [[ "false" != "$TRAVIS_PULL_REQUEST" ]]; then
	echo "Not deploying pull requests."
	exit
fi

# 只部署一次
if [[ ! $WP_PULUGIN_DEPLOY ]]; then
	echo "Not deploying."
	exit
fi

# SVN 仓库未定义，发出提醒
if [[ ! $SVN_REPO ]]; then
	echo "SVN repo is not specified."
	exit
fi

#####################################################
# 比较版本，如果两个版本不一样，退出
#####################################################
#if [ "$READMEVERSION" != "$PLUGINVERSION" ]; then
#    echo "Versions don't match. Exiting....";
#    exit 1
#fi

#####################################################
# 开始部署
#####################################################
echo "Starting deploy..."

mkdir build

cd build
BASE_DIR=$(pwd)

echo "Checking out trunk from $SVN_REPO ..."
svn co -q $SVN_REPO/trunk

echo "Getting clone from $GH_REF to $SVN_REPO ..."
git clone -q $GH_REF ./git

cd ./git

if [ -e "bin/build.sh" ]; then
	echo "Starting bin/build.sh."
	bash bin/build.sh
fi

cd $BASE_DIR

# 同步 git 仓库到 SVN
echo "Syncing git repository to svn"
rsync -a --exclude=".svn" --checksum --delete ./git/ ./trunk/

rm -fr ./git

#####################################################
# 忽略文件
#####################################################
cd ./trunk

if [ -e ".distignore" ]; then
	echo "svn propset form .distignore"
	svn propset -q -R svn:ignore -F .distignore .

else
	if [ -e ".svnignore" ]; then
		echo "svn propset"
		svn propset -q -R svn:ignore -F .svnignore .
	fi
fi


#####################################################
# 执行 SVN 操作
#####################################################
echo "Run svn add"
svn st | grep '^!' | sed -e 's/\![ ]*/svn del -q /g' | sh
echo "Run svn del"
svn st | grep '^?' | sed -e 's/\?[ ]*/svn add -q /g' | sh


#####################################################
# 如果设置了用户名密码，提交到仓库，必须是 Tag 才能提交
#####################################################
cd ../
svn copy trunk/ tags/$READMEVERSION/
svn stat

svn ci --no-auth-cache --username $WP_ORG_USERNAME --password $WP_ORG_PASSWORD svn -m "Deploy version $READMEVERSION"