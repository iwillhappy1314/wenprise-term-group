#!/bin/bash

#####################################################
# 定义变量，需要根据插件和用户修改
#####################################################
MAINFILE="wenprise-term-group.php"
WP_ORG_USERNAME="iwillhappy1314"
SVN_REPO="https://plugins.svn.wordpress.org/wenprise-term-group/"
GIT_REPO=https://github.com/${TRAVIS_REPO_SLUG}.git


#####################################################
# 部署检查
#####################################################

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
# 开始部署
#####################################################
echo "Starting deploy..."

# 创建部署所使用的目录
mkdir build

cd build
BUILT_DIR=$(pwd)

# 检出 SVN
echo "Checking out trunk from $SVN_REPO ..."
svn co -q $SVN_REPO ./svn

# 检出 Git，已经有了，是不是不需要再来一遍了，或者直接 checkout?
echo "Getting clone from $GIT_REPO to $SVN_REPO ..."
git clone -q $GIT_REPO ./git

# 如果设置了构建脚本，开始构建
cd $BUILT_DIR/git

if [ -e "bin/build.sh" ]; then
	echo "Starting bin/build.sh."
	bash bin/build.sh
fi

#####################################################
# 同步文件
#####################################################
# 同步 git 仓库到 SVN
cd $BUILT_DIR
echo "Syncing git repository to svn"
rsync -a --exclude=".svn" --checksum --delete ./git/ ./svn/trunk/

rm $BUILT_DIR/svn/trunk/.git -Rf

#####################################################
# 比较版本，如果两个版本不一样，退出
#####################################################
READMEVERSION=`grep "Stable tag" $BUILT_DIR/svn/trunk/readme.txt | awk '{ print $NF}'`
PLUGINVERSION=`grep "Version:" $BUILT_DIR/svn/trunk/$MAINFILE | awk '{ print $NF}'`

if [ "$READMEVERSION" != "$PLUGINVERSION" ]; then
    echo "Versions don't match. Exiting....";
    exit 1
fi

#####################################################
# 设置忽略文件、删除忽略的文件
#####################################################
cd $BUILT_DIR/svn/trunk

# 设置 svn 忽略
if [ -e ".svnignore" ]; then
    echo "svn propset form .svnignore"
    svn propset -q -R svn:ignore -F .svnignore
fi

# 删除忽略的文件
for file in $(cat ".svnignore" 2>/dev/null)
do
    rm $file -Rf
done


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
echo "svn 目录";
ls $BUILT_DIR/svn -la

echo "svn trunk 目录";
ls $BUILT_DIR/svn/trunk -la

# 复制文件到 tag，如果 Tag 不存在，跳过
TAG=$(svn ls "SVN_REPO/tags/$READMEVERSION")
error=$?
if [ $error != 0 ]; then
    svn copy $BUILT_DIR/svn/trunk/ $BUILT_DIR/svn/tags/$READMEVERSION/
    exit 1
fi

cd $BUILT_DIR/svn
svn stat
svn ci --no-auth-cache --username $WP_ORG_USERNAME --password $WP_ORG_PASSWORD -m "Deploy version $READMEVERSION"