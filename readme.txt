# Plugin Name #
Contributors: iwillhappy1314
Donate link: https://www.wpzhiku.com/
Tags: SEO, term_group, term_alias, tag, 301
Requires at least: 3.7
Requires PHP: 5.6.0
Tested up to: 5.2
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

当网站有同义词标签时，此插件可以让同义词标签页面 301 跳转到主标签页面。

## Description ##

当网站有同义词标签时，此插件可以让同义词标签存档页面 301 跳转到主标签。


### 插件的主要功能 ###

* 设置某个标签为主标签
* 为同义词标签选择主标签
* 设置了主标签的同义词标签存档页面 301 跳转到主标签
* 可以取消各项设置

### 为什么需要这个插件？###

WordPress 中的标签可以写得比较随意，经常会出现同义词的情况，比如我的网站（wpzhiku.com）中，“网站加速”和“网站提速”这两个关键词，其实是一个意思，这样会导致站点标签存档页面 SEO 竞争的情况。

这个插件可以设置一个主标签，然后为主标签的同义词标签选择这个主标签，再打开同义词标签的时候，就会自动以 301 方式跳转到主标签，比如设置“网站加速”为主标签，在“网站提速”标签中选择网站加速为主标签，用户访问网站提速标签时，会自动跳转到网站加速标签存档页面。

### 什么情况下使用这个插件？ ###

您的网站出现了以下几种情况的时候，可以考虑使用本插件。

- 如果你觉得网站中有较多的同义词标签、并且标签存档页面的文章数量都比较少
- 两个同义词标签在搜索引擎中都有排名，但是排名的不是很高，说明权重被分散了，这时候，合并同义词标签对提高页面排名会有帮助
- 需要修改标签别名，旧的标签又不能直接删除，可以新建一个标签设置为主标签，然后把旧的标签跳转过来


## Installation ##

1. 上传插件到`/wp-content/plugins/` 目录，或在 WordPress 安装插件界面搜索 "Wenprise Term Group"，点击安装。
2. 在插件管理菜单激活插件

## Frequently Asked Questions ##

## Screenshots ##

## Changelog ##

= 1.0.3 =
* bugfix

= 1.0 =
* 初次发布、支持设置主标签、为同义词标签选择主标签、支持同义词标签自动跳转到主标签