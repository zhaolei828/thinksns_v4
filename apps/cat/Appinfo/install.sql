-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 09 月 11 日 07:13
-- 服务器版本: 5.5.24-log
-- PHP 版本: 5.3.13

SET time_zone = "+00:00";


--
-- 表的结构 `ts_adspace_ad`
--

CREATE TABLE IF NOT EXISTS `ts_adspace_ad` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '广告ID，主键',
  `title` varchar(255) DEFAULT NULL COMMENT '广告标题',
  `place` varchar(50) NOT NULL DEFAULT '0' COMMENT '广告位置：0-中部；1-头部；2-左下；3-右下；4-底部；5-右上；',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效；0-无效；1-有效；',
  `is_closable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关闭，目前没有使用。',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `mtime` int(11) DEFAULT NULL COMMENT '更新时间',
  `display_order` smallint(2) NOT NULL DEFAULT '0' COMMENT '排序值',
  `display_type` tinyint(1) unsigned DEFAULT '1' COMMENT '广告类型：1 - HTML；2 - 代码；3 - 轮播',
  `content_html` text COMMENT '广告位内容',
  `content_code` text COMMENT '广告位内容',
  `content_picture0` text COMMENT '广告位内容',
  `content_link0` text,
  `content_picture1` text COMMENT '广告位内容',
  `content_link1` text,
  `content_picture2` text COMMENT '广告位内容',
  `content_link2` text,
  `content_picture3` text COMMENT '广告位内容',
  `content_link3` text,
  `content_picture4` text COMMENT '广告位内容',
  `content_link4` text,
  `content_picture5` text COMMENT '广告位内容',
  `content_link5` text,
  `hit` int(11) NOT NULL COMMENT '点击量',
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 CHARACTER SET utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ts_adspace_place`
--

CREATE TABLE IF NOT EXISTS `ts_adspace_place` (
  `place_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`place_id`)
) ENGINE=MyISAM  AUTO_INCREMENT=8 CHARACTER SET utf8;

--
-- 转存表中的数据 `ts_adspace_place`
--

INSERT INTO `ts_adspace_place` (`place_id`, `name`, `description`) VALUES
(6, 'cat_li_right_up', '分类信息列表页推荐上方广告'),
(2, 'cat_index_right_up', '分类信息首页右侧推荐上方广告'),
(3, 'cat_index_right_down', '分类信息首页右侧推荐下方广告'),
(4, 'cat_index_ad_down', '分类信息首页公告下方广告'),
(5, 'cat_li_post_right', '分类信息列表页发布按钮右侧广告'),
(7, 'cat_li_right_down', '分类信息列表页推荐下方广告');

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat`
--

CREATE TABLE IF NOT EXISTS `ts_cat` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `sort` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `ext` text NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  AUTO_INCREMENT=4 CHARACTER SET utf8;

--
-- 转存表中的数据 `ts_cat`
--

INSERT INTO `ts_cat` (`cat_id`, `title`, `sort`, `pid`, `ext`) VALUES
(1, '商品', 2, 0, ''),
(2, '兼职', 1, 0, ''),
(3, '人才', 3, 0, '');

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_com`
--

CREATE TABLE IF NOT EXISTS `ts_cat_com` (
  `com_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `cTime` int(11) NOT NULL,
  `content` text NOT NULL,
  `info_id` int(11) NOT NULL,
  PRIMARY KEY (`com_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_data`
--

CREATE TABLE IF NOT EXISTS `ts_cat_data` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `info_id` int(11) NOT NULL,
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_entity`
--

CREATE TABLE IF NOT EXISTS `ts_cat_entity` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `can_post_gid` varchar(50) NOT NULL,
  `can_read_gid` varchar(50) NOT NULL,
  `tpl3` text NOT NULL,
  `tpl1` text NOT NULL,
  `tpl2` text NOT NULL,
  `alias` varchar(20) NOT NULL,
  `tpl_detail` text NOT NULL,
  `tpl_list` text NOT NULL,
  `use_detail` int(11) NOT NULL,
  `use_list` int(11) NOT NULL,
  `des1` text NOT NULL,
  `des2` text NOT NULL,
  `des3` text NOT NULL,
  `can_over` int(11) NOT NULL COMMENT '允许设置截止日期',
  `show_nav` int(11) NOT NULL,
  `show_post` int(11) NOT NULL,
  `show_index` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `can_rec` tinyint(4) NOT NULL,
  `rec_entity` varchar(50) NOT NULL,
  `need_active` tinyint(4) NOT NULL,
  `pb_color` varchar(20) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  AUTO_INCREMENT=6 CHARACTER SET utf8;

--
-- 转存表中的数据 `ts_cat_entity`
--

INSERT INTO `ts_cat_entity` (`entity_id`, `name`, `can_post_gid`, `can_read_gid`, `tpl3`, `tpl1`, `tpl2`, `alias`, `tpl_detail`, `tpl_list`, `use_detail`, `use_list`, `des1`, `des2`, `des3`, `can_over`, `show_nav`, `show_post`, `show_index`, `sort`, `can_rec`, `rec_entity`, `need_active`, `pb_color`) VALUES
(1, 'Job', '3,1', '3,1', '', '<div class="mb10 pd10 underline">\r\n<div class="mb10"><a href="{$[url]}" class="f16px cblue" style="margin-right:10px">{$name}</a> <span class="cgrey f14px">待遇：{$reward}  \r\n工作地点： {$place}</span></div>\r\n<div class="cat_ul_tm mb10">{$[cTime]}</div>\r\n<div class="cat_des">{$des}\r\n</div>\r\n\r\n<div class="cat_head_pic"><a target="_blank" href="{$[user_space_url]}" event-node="face_card" uid="{$[user_uid]}"><img class="cat_head_size" src="{$[user_avatar_middle]}"><br/>\r\n<div class="cat_uname">{$[user_uname]}</a></div>\r\n</div>\r\n<div class="clearfix"/>\r\n\r\n</div>', '<a href="{$[url]}">{$name}</a>', '岗位', '<div id="col5" class="left cat_yunmai_d_left">\r\n<div class="pd20">\r\n<h2 class="cat_yunme_title">\r\n{$j_name}\r\n</h2>\r\n<div class="c999 mt5">\r\n{$[cTime]}\r\n</div>\r\n<div class="br5 cat_yunmai_det">\r\n<ul>\r\n<li class="cat_yunmai_co">公司名称：{$company}</li>\r\n<li>工作地点：{$place}</li>\r\n<li>薪资待遇：{$reward}</li>\r\n<li>招聘人数：{$num}</li>\r\n<li>EMail:{$email}</li>\r\n</ul>\r\n<div class="clearfix"></div>\r\n</div>\r\n<h3 class="cat_yunmai_title">\r\n职位描述：\r\n</h3>\r\n<div class="cat_yunmai_des">\r\n{$des}\r\n</div>\r\n<div class="clearfix underline mb10 mt10"></div>\r\n{$[fav_btn]}\r\n</div>\r\n</div>\r\n<div id="col3" class="left ">\r\n<div class="pd20">\r\n<div class=""><a target="_blank" class="left mr10" href="{$[user_space_url]}" event-node="face_card" uid="{$[user_uid]}"><img class="br3" src="{$[user_avatar_small]}"><br/>\r\n<div class="cat_uname" style="text-align:left">{$[user_uname]}</a>\r\n<br/>\r\n<span class="c333">{$[user_location]}</span>\r\n</div>\r\n\r\n</div>\r\n</div></div>\r\n<div class="clearfix"></div>', '<div class="mb10 pd10 underline">\r\n<div class="mb10"><a target="_blank" href="{$[url]}" class="f16px cblue" style="margin-right:10px">{$j_name}</a> <span class="cgrey f14px">待遇：{$reward}  \r\n工作地点： {$place}</span>\r\n  {$[fav_btn]}\r\n</div>\r\n<div class="cat_ul_tm mb10">{$[cTime]}</div>\r\n<div class="cat_des ">{$des}\r\n\r\n</div>\r\n\r\n<div class="cat_head_pic"><a target="_blank" href="{$[user_space_url]}" event-node="face_card" uid="{$[user_uid]}"><img class="cat_head_size" src="{$[user_avatar_middle]}"><br/>\r\n<div class="cat_uname">{$[user_uname]}</a></div>\r\n</div>\r\n<div class="clearfix"/>\r\n\r\n\r\n</div>', -1, 0, '<div class="right_box">\r\n	<div class="boxInvite br5">\r\n		<h3>\r\n			小提示\r\n		</h3>\r\n云招聘时代，宗旨是更开放，更分享，更合理，如何更好的发布职位动态，云招聘来给您支几招：<br />\r\n<br />\r\n1，尽量详细的介绍关于即将发布职位的信息，让应聘者在第一时间了解更多关于企业的信息<br />\r\n<br />\r\n2，在职位描述中填写关于职位的就职要求，公司其他待遇之类，可以吸引应聘者的信息。为了展示更多信息，在职位描述中可以附图，图文并茂最佳。<br />\r\n	</div>\r\n</div>', '0', '', 0, 1, 1, 1, 0, 0, '', 0, '#006633'),
(2, 'House', '0', '0', '', '<div class="fe_main">\r\n<div class="left mg10"><img src="{$zhaopian1}" class="pic1"></div>\r\n        <div class="left mg10 fe_detail">\r\n            <div class="fe_title mb10"><a href="{$[url]}" target="_blank">{$biaoti}</a></div>\r\n            <div class="fe_p">\r\n                {$yijuhua}<br/>{$daxiao}平米\r\n\r\n            </div>\r\n\r\n        </div>\r\n        <div class="left mg10 fe_m">\r\n            <div class="fe_money">\r\n                {$zujin}\r\n            </div>\r\n            <div class="fe_det">\r\n                {$shi}室{$ting}厅{$wei}卫\r\n            </div>\r\n        </div>\r\n        <div class="left mg10 fe_time">\r\n            {$[cTime]}\r\n        </div>\r\n<div class="clearfix"></div>\r\n</div>', '', '房产', '', '<div class="mb10 pd10 underline">\r\n<div class="mb10"><a href="{$[url]}" class="f16px cblue" style="margin-right:10px">{$title}</a> <span class="cgrey f14px">租金：{$money}  \r\n形式： {$home_type}</span></div>\r\n<div class="cat_ul_tm mb10">{$[cTime]}</div>\r\n<div class="cat_des">{$des}</div><div class="cat_head_pic"><a target="_blank" href="{$[user_space_url]}" event-node="face_card" uid="{$[user_uid]}"><img class="cat_head_size" src="{$[user_avatar_middle]}"><br/>\r\n<div class="cat_uname">{$[user_uname]}</a></div>\r\n</div>\r\n<div class="clearfix"/>\r\n\r\n</div>', 0, 1, '', '0', '', 1, 1, 1, 1, 500, 0, '', 0, ''),
(3, 'PTJob', '0', '0', '', '', '', '兼职', '', '', 0, -1, '', '', '', 1, 1, 1, 1, 0, 1, '', 0, '#cc00cc'),
(4, 'Ad.', '1', '0', '', '', '', '公告', '', '<div class="mb10" style="margin-bottom:10px;font-size:14px;\r\npadding:10px;line-height:18px">\r\n<div style="float:left;background:rgb(77,157,221);padding:2px 8px 2px 8px;border-radius:14px;color:white;margin-right:10px">{$[cTimeD]}</div>\r\n{$content}</div>', 0, 0, '', '', '', 0, 1, 1, 0, 0, 0, '', 0, '#ff9900'),
(5, 'jianli', '0', '0', '', '', '', '简历', '', '<div>a{$xingbie}</div>\r\n<a href="{$[url]}">{$xingming}</a>\r\n空闲的上午{$shangwukongxian}', 0, -1, '<p>\r\n	<br />\r\n</p>', '', '', 0, 0, 1, 0, 0, 0, '', 0, '#3366ff');

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_fav`
--

CREATE TABLE IF NOT EXISTS `ts_cat_fav` (
  `fav_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `cTime` int(11) NOT NULL,
  `info_id` int(11) NOT NULL,
  PRIMARY KEY (`fav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_field`
--

CREATE TABLE IF NOT EXISTS `ts_cat_field` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `input_type` int(11) NOT NULL,
  `option` text NOT NULL,
  `limit1` varchar(500) NOT NULL,
  `limit2` varchar(500) NOT NULL,
  `limit3` varchar(500) NOT NULL,
  `limit4` varchar(500) NOT NULL,
  `can_search` int(11) NOT NULL,
  `alias` varchar(30) NOT NULL,
  `name` varchar(20) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `can_empty` int(11) NOT NULL,
  `over_hidden` int(11) NOT NULL COMMENT '到期后自动隐藏',
  `default_value` text NOT NULL,
  `tip` text NOT NULL,
  `args` text NOT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  AUTO_INCREMENT=58 CHARACTER SET utf8;

--
-- 转存表中的数据 `ts_cat_field`
--

INSERT INTO `ts_cat_field` (`field_id`, `input_type`, `option`, `limit1`, `limit2`, `limit3`, `limit4`, `can_search`, `alias`, `name`, `entity_id`, `sort`, `can_empty`, `over_hidden`, `default_value`, `tip`, `args`) VALUES
(1, 0, '', '', '', '', '', 1, '岗位名', 'j_name', 1, 1000, 0, 0, '', '岗位名称', 'min=2&max=20&error=必须输入2-20个汉字'),
(2, 2, '{"tip":"选择待遇","data":{"0":"1000-2000元","1":"2001-5000元","20":"5001-20000元","3":"面议"}}', '', '', '', '', 1, '待遇', 'reward', 1, 0, 0, 0, '', '', 'need=1&error=必须选择待遇'),
(3, 2, '{"tip":"请选择城市","data":{"1":"杭州","2":"北京","30":"印度"}}', '', '', '', '', 1, '地点', 'place', 1, 0, 0, 0, '', '', 'need=1&error=必须选择地点'),
(4, 6, '', '', '', '', '', 1, '描述', 'des', 1, -4, 1, 0, '', '', ''),
(5, 0, '', '', '', '', '', 1, '公司名称', 'company', 1, 2, 0, 0, '', '', 'min=4&max=40&error=只能输入4-40个汉字'),
(6, 0, '', '', '', '', '', 0, '联系方式', 'phone', 1, 0, 0, 0, '', '', 'min=1&error=必须填写联系方式'),
(7, 0, '', '', '', '', '', 0, 'Email', 'email', 1, 0, 0, 0, '', '', 'min=1&error=必须填写电子邮箱'),
(8, 0, '', '', '', '', '', 1, '招聘人数', 'num', 1, 0, 0, 0, '', '', 'min=1&error=必须填写招聘人数'),
(9, 0, '', '', '', '', '', 1, '室', 'shi', 2, 997, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(10, 0, '', '', '', '', '', 1, '卫', 'wei', 2, 996, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(11, 0, '', '', '', '', '', 0, '大小', 'daxiao', 2, 995, 0, 0, '', '平米', 'need=1&min=1&error=请输入内容'),
(12, 0, '', '', '', '', '', 1, '楼层', 'louceng', 2, 992, 0, 0, '第  层，共  层', '写清楚第几层，共几层', 'need=1&min=1&error=请输入内容'),
(13, 6, '', '', '', '', '', 0, '描述', 'des', 2, 0, 1, 0, '', '', ''),
(14, 0, '', '', '', '', '', 1, '标题', 'title', 3, 1000, 0, 0, '', '请输入标题', 'min=1&error=请输入内容'),
(15, 6, '', '', '', '', '', 0, '内容', 'des', 5, 0, 0, 0, '', '', 'min=1&error=请输入内容'),
(16, 6, '', '', '', '', '', 1, '介绍', 'jieshao', 3, -999, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(17, 0, '', '', '', '', '', 1, '工作地点', 'pos', 3, 0, 0, 0, '', '', 'min=1&error=请输入内容'),
(18, 0, '', '', '', '', '', 0, '联系方式', 'contact', 3, 0, 0, 1, '', '', 'min=1&error=请输入内容'),
(19, 0, '', '', '', '', '', 0, '姓名', 'xingming', 6, 100, 0, 0, '', '2-10个汉字', 'min=2&max=10&error=请输入姓名,2-10个汉字'),
(20, 3, '{"1":"男","2":"女"}', '', '', '', '', 1, '性别', 'xingbie', 6, 99, 0, 0, '', '', 'need=1&error=请选择性别'),
(21, 5, '', '', '', '', '', 0, '加入日期', 'jiaruriqi', 6, 99, 0, 0, '', '', 'need=1&error=请选择加入日期'),
(22, 1, '', '', '', '', '', 0, '兼职经历', 'jianzhijingli', 6, 98, 0, 0, '', '请把自己的兼职经历填写一下', 'min=1&error=请输入内容'),
(23, 0, '', '', '', '', '', 0, '户籍地址', 'hujidizhi', 6, 95, 0, 0, '', '', 'min=1&error=请输入内容'),
(24, 0, '', '', '', '', '', 0, '身高', 'shengao', 6, 91, 0, 0, '', 'cm', 'min=1&error=请输入内容'),
(25, 0, '', '', '', '', '', 0, '体重', 'tizhong', 6, 90, 0, 0, '', 'kg', 'min=1&error=请输入内容'),
(26, 0, '', '', '', '', '', 0, '手机号码', 'shoujihaoma', 6, 90, 0, 0, '', '', 'min=1&error=请输入内容'),
(27, 0, '', '', '', '', '', 0, 'QQ号码', 'qqhaoma', 6, 89, 0, 0, '', '', 'min=1&error=请输入内容'),
(28, 0, '', '', '', '', '', 0, '身份证号', 'shenfenzhenghao', 6, 87, 0, 0, '', '', 'min=1&error=请输入内容'),
(29, 3, '{"1":"有","2":"无"}', '', '', '', '', 0, '有无电脑', 'youwudiannao', 6, 85, 0, 0, '', '', 'need=1&error=请输入内容'),
(30, 6, '', '', '', '', '', 0, '自我介绍', 'ziwojieshao', 6, 84, 0, 0, '', '用一句话介绍自己', 'need=1&min=1&error=请输入内容'),
(31, 5, '', '', '', '', '', 0, '生日', 'shengri', 6, 84, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(32, 4, '{"1":"周一","2":"周二","3":"周三","4":"周四","5":"周五","6":"周六","7":"周日"}', '', '', '', '', 1, '空闲上午', 'shangwukongxian', 6, 70, 0, 0, '', '选择有空的时间', 'need=1&min=1&error=请输入内容'),
(33, 4, '{"1":"参数1","2":"参数2","3":"参数3"}', '', '', '', '', 1, '多选', 'duoxuan', 7, 0, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(34, 7, '', '', '', '', '', 0, '个人照片', 'zhaopian', 6, 99, 0, 0, '', '个人照片，清晰免冠照', 'need=1&min=1&error=请输入内容'),
(35, 4, '{"1":"周一","2":"周二","3":"周三","4":"周四","5":"周五","6":"周六","7":"周日"}', '', '', '', '', 1, '下午有空', 'xiawuyoukong', 6, 0, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(36, 4, '{"1":"发单","2":"派发","3":"促销","4":"市调","5":"礼仪","6":"模特","7":"舞蹈","8":"歌手","9":"乐器","10":"校园代理","11":"服从调配"}', '', '', '', '', 1, '兼职意向', 'jianzhiyixiang', 6, 0, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(37, 0, '', '', '', '', '', 1, '期望待遇', 'qiwangdaiyu', 6, 0, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(38, 0, '', '', '', '', '', 0, '未来发展城市', 'weilaifazhan', 6, 0, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(39, 3, '{"1":"个人","2":"中介"}', '', '', '', '', 1, '身份', 'shenfen', 2, 1000, 0, 0, '', '', 'need=1&min=1&error=请选择身份'),
(40, 3, '{"1":"整套出租","2":"单间出租","3":"床位出租"}', '', '', '', '', 1, '出租方式', 'fangshi', 2, 999, 0, 0, '', '', 'need=1&min=1&error=请选择出租方式'),
(41, 0, '', '', '', '', '', 1, '小区名称', 'xiaoqu', 2, 998, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(42, 0, '', '', '', '', '', 1, '厅', 'ting', 2, 997, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(43, 2, '{"tip":"请选择类型","data":{"1":"普通住宅","2":"商住两用","3":"公寓","4":"平房","5":"别墅","6":"其他"}}', '', '', '', '', 1, '类型', 'leixing', 2, 991, 1, 0, '', '', ''),
(44, 2, '{"tip":"装修情况","data":{"1":"毛坯","2":"简单装修","3":"中等装修","4":"精装修","5":"豪华装修"}}', '', '', '', '', 1, '装修情况', 'zhuangxiu', 2, 988, 1, 0, '', '', ''),
(45, 0, '', '', '', '', '', 1, '朝向', 'chaoxiang', 2, 977, 0, 0, '', '', 'need=1&min=1&error=请选择朝向'),
(46, 0, '', '', '', '', '', 1, '租金', 'zujin', 2, 955, 0, 0, '面议', '最好写清楚价格', 'need=1&min=1&error=请输入内容'),
(47, 0, '', '', '', '', '', 0, '支付方式', 'zhifu', 2, 944, 0, 0, '', '写清楚押几付几', 'need=1&min=1&error=请输入内容'),
(48, 0, '', '', '', '', '', 1, '标题', 'biaoti', 2, 99999, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(49, 0, '', '', '', '', '', 0, '一句话广告', 'yijuhua', 2, 901, 0, 0, '', '', 'need=1&min=1&error=请输入内容'),
(50, 7, '', '', '', '', '', 0, '照片1', 'zhaopian1', 2, 889, 1, 0, '', '', ''),
(51, 7, '', '', '', '', '', 0, '照片2', 'zhaopian2', 2, 888, 1, 0, '', '', ''),
(52, 7, '', '', '', '', '', 0, '照片3', 'zhaopian3', 2, 887, 1, 0, '', '', ''),
(53, 7, '', '', '', '', '', 0, '照片4', 'zhaopian4', 2, 886, 1, 0, '', '', ''),
(54, 7, '', '', '', '', '', 0, '照片5', 'zhaopian5', 2, 885, 1, 0, '', '', ''),
(55, 0, '', '', '', '', '', 0, '联系电话', 'lianxidianhua', 2, 884, 0, 1, '', '输入手机或者座机号码', 'need=1&min=1&error=请输入内容'),
(56, 0, '', '', '', '', '', 0, '联系人', 'lianxiren', 2, 883, 0, 0, '', '联系人的称呼', 'need=1&min=1&error=请输入'),
(57, 1, '', '', '', '', '', 1, '内容', 'content', 4, 0, 0, 0, '', '', 'need=1&min=1&max=2000&error=请输入内容');

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_info`
--

CREATE TABLE IF NOT EXISTS `ts_cat_info` (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `cTime` int(11) NOT NULL,
  `read` int(11) NOT NULL,
  `sub` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `over_time` int(11) NOT NULL COMMENT '截止时间',
  `rate` float NOT NULL,
  `active` tinyint(4) NOT NULL,
  `top` tinyint(4) NOT NULL,
  `recom` tinyint(4) NOT NULL,
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_rate`
--

CREATE TABLE IF NOT EXISTS `ts_cat_rate` (
  `rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `cTime` int(11) NOT NULL,
  `info_id` int(11) NOT NULL,
  `score` float NOT NULL,
  PRIMARY KEY (`rate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_read`
--

CREATE TABLE IF NOT EXISTS `ts_cat_read` (
  `read_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `cTime` int(11) NOT NULL,
  `info_id` int(11) NOT NULL,
  PRIMARY KEY (`read_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ts_cat_send`
--

CREATE TABLE IF NOT EXISTS `ts_cat_send` (
  `send_id` int(11) NOT NULL AUTO_INCREMENT,
  `send_uid` int(11) NOT NULL,
  `rec_uid` int(11) NOT NULL,
  `cTime` int(11) NOT NULL,
  `s_info_id` int(11) NOT NULL,
  `info_id` int(11) NOT NULL,
  `readed` tinyint(4) NOT NULL,
  PRIMARY KEY (`send_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARACTER SET utf8;







INSERT INTO `ts_system_config` ( `list`, `key`, `value`, `mtime`) VALUES
('pageKey', 'cat_Admin_index', 'a:6:{s:3:"key";a:2:{s:3:"CSS";s:3:"CSS";s:2:"JS";s:2:"JS";}s:8:"key_name";a:2:{s:3:"CSS";s:0:"";s:2:"JS";s:0:"";}s:8:"key_type";a:2:{s:3:"CSS";s:8:"textarea";s:2:"JS";s:8:"textarea";}s:11:"key_default";a:2:{s:3:"CSS";s:0:"";s:2:"JS";s:0:"";}s:9:"key_tishi";a:2:{s:3:"CSS";s:72:"请将自定义样式粘贴在这里，作为对自定义模板的支持";s:2:"JS";s:114:"请将自定义JS粘贴在这里，作为对自定义模板的支持,请勿使用单引号，否则无法保存。";}s:14:"key_javascript";a:2:{s:3:"CSS";s:0:"";s:2:"JS";s:0:"";}}', '2013-06-28 16:20:51'),
('pageKey', 'cat_Admin_entity', 'a:4:{s:3:"key";a:4:{s:9:"entity_id";s:9:"entity_id";s:4:"name";s:4:"name";s:5:"alias";s:5:"alias";s:2:"do";s:2:"do";}s:8:"key_name";a:4:{s:9:"entity_id";s:2:"ID";s:4:"name";s:6:"名称";s:5:"alias";s:6:"别名";s:2:"do";s:6:"操作";}s:10:"key_hidden";a:4:{s:9:"entity_id";s:1:"0";s:4:"name";s:1:"0";s:5:"alias";s:1:"0";s:2:"do";s:1:"0";}s:14:"key_javascript";a:4:{s:9:"entity_id";s:0:"";s:4:"name";s:0:"";s:5:"alias";s:0:"";s:2:"do";s:0:"";}}', '2013-06-30 15:38:38'),
('pageKey', 'cat_Admin_field', 'a:4:{s:3:"key";a:6:{s:8:"field_id";s:8:"field_id";s:4:"name";s:4:"name";s:5:"alias";s:5:"alias";s:4:"sort";s:4:"sort";s:10:"input_type";s:10:"input_type";s:2:"do";s:2:"do";}s:8:"key_name";a:6:{s:8:"field_id";s:8:"字段ID";s:4:"name";s:9:"字段名";s:5:"alias";s:12:"字段别名";s:4:"sort";s:6:"排序";s:10:"input_type";s:12:"输入方式";s:2:"do";s:6:"操作";}s:10:"key_hidden";a:6:{s:8:"field_id";s:1:"0";s:4:"name";s:1:"0";s:5:"alias";s:1:"0";s:4:"sort";s:1:"0";s:10:"input_type";s:1:"0";s:2:"do";s:1:"0";}s:14:"key_javascript";a:6:{s:8:"field_id";s:0:"";s:4:"name";s:0:"";s:5:"alias";s:0:"";s:4:"sort";s:0:"";s:10:"input_type";s:0:"";s:2:"do";s:0:"";}}', '2013-07-01 07:21:37'),
('pageKey', 'cat_Admin_com', 'a:4:{s:3:"key";a:6:{s:6:"com_id";s:6:"com_id";s:4:"user";s:4:"user";s:4:"info";s:4:"info";s:7:"content";s:7:"content";s:5:"cTime";s:5:"cTime";s:2:"Do";s:2:"Do";}s:8:"key_name";a:6:{s:6:"com_id";s:8:"评论ID";s:4:"user";s:12:"发表用户";s:4:"info";s:12:"所在信息";s:7:"content";s:6:"内容";s:5:"cTime";s:12:"发表时间";s:2:"Do";s:6:"操作";}s:10:"key_hidden";a:6:{s:6:"com_id";s:1:"0";s:4:"user";s:1:"0";s:4:"info";s:1:"0";s:7:"content";s:1:"0";s:5:"cTime";s:1:"0";s:2:"Do";s:1:"0";}s:14:"key_javascript";a:6:{s:6:"com_id";s:0:"";s:4:"user";s:0:"";s:4:"info";s:0:"";s:7:"content";s:0:"";s:5:"cTime";s:0:"";s:2:"Do";s:0:"";}}', '2013-07-14 08:25:19'),
('pageKey', 'cat_Admin_addField', 'a:6:{s:3:"key";a:13:{s:8:"field_id";s:8:"field_id";s:9:"entity_id";s:9:"entity_id";s:4:"name";s:4:"name";s:10:"can_search";s:10:"can_search";s:5:"alias";s:5:"alias";s:10:"input_type";s:10:"input_type";s:4:"args";s:4:"args";s:6:"option";s:6:"option";s:3:"tip";s:3:"tip";s:11:"over_hidden";s:11:"over_hidden";s:4:"sort";s:4:"sort";s:13:"default_value";s:13:"default_value";s:9:"can_empty";s:9:"can_empty";}s:8:"key_name";a:13:{s:8:"field_id";s:8:"字段ID";s:9:"entity_id";s:12:"所属实体";s:4:"name";s:9:"字段名";s:10:"can_search";s:12:"能够搜索";s:5:"alias";s:12:"字段别名";s:10:"input_type";s:12:"输入方式";s:4:"args";s:12:"节点参数";s:6:"option";s:12:"参数内容";s:3:"tip";s:12:"提示信息";s:11:"over_hidden";s:12:"到期处理";s:4:"sort";s:6:"排序";s:13:"default_value";s:9:"默认值";s:9:"can_empty";s:12:"是否必填";}s:8:"key_type";a:13:{s:8:"field_id";s:4:"word";s:9:"entity_id";s:6:"select";s:4:"name";s:4:"text";s:10:"can_search";s:6:"select";s:5:"alias";s:4:"text";s:10:"input_type";s:6:"select";s:4:"args";s:8:"textarea";s:6:"option";s:8:"textarea";s:3:"tip";s:4:"text";s:11:"over_hidden";s:6:"select";s:4:"sort";s:4:"text";s:13:"default_value";s:4:"text";s:9:"can_empty";s:6:"select";}s:11:"key_default";a:13:{s:8:"field_id";s:0:"";s:9:"entity_id";s:0:"";s:4:"name";s:0:"";s:10:"can_search";s:0:"";s:5:"alias";s:0:"";s:10:"input_type";s:0:"";s:4:"args";s:43:"need=1&min=1&max=2000&error=请输入内容";s:6:"option";s:0:"";s:3:"tip";s:0:"";s:11:"over_hidden";s:0:"";s:4:"sort";s:0:"";s:13:"default_value";s:0:"";s:9:"can_empty";s:1:"0";}s:9:"key_tishi";a:13:{s:8:"field_id";s:0:"";s:9:"entity_id";s:0:"";s:4:"name";s:55:"必须为英文，用于模板输出，禁止使用name";s:10:"can_search";s:15:"可用于搜索";s:5:"alias";s:21:"中文，用于显示";s:10:"input_type";s:12:"输入形式";s:4:"args";s:18:"参考技术手册";s:6:"option";s:40:"Json格式数据，请参考技术手册";s:3:"tip";s:0:"";s:11:"over_hidden";s:0:"";s:4:"sort";s:24:"整数，越大越靠前";s:13:"default_value";s:0:"";s:9:"can_empty";s:0:"";}s:14:"key_javascript";a:13:{s:8:"field_id";s:0:"";s:9:"entity_id";s:0:"";s:4:"name";s:0:"";s:10:"can_search";s:0:"";s:5:"alias";s:0:"";s:10:"input_type";s:0:"";s:4:"args";s:0:"";s:6:"option";s:0:"";s:3:"tip";s:0:"";s:11:"over_hidden";s:0:"";s:4:"sort";s:0:"";s:13:"default_value";s:0:"";s:9:"can_empty";s:0:"";}}', '2013-09-07 12:53:46'),
('pageKey', 'cat_Admin_addAd', 'a:6:{s:3:"key";a:24:{s:5:"ad_id";s:5:"ad_id";s:5:"title";s:5:"title";s:5:"place";s:5:"place";s:9:"is_active";s:9:"is_active";s:11:"is_closable";s:11:"is_closable";s:5:"ctime";s:5:"ctime";s:5:"mtime";s:5:"mtime";s:13:"display_order";s:13:"display_order";s:12:"display_type";s:12:"display_type";s:12:"content_html";s:12:"content_html";s:12:"content_code";s:12:"content_code";s:16:"content_picture0";s:16:"content_picture0";s:13:"content_link0";s:13:"content_link0";s:16:"content_picture1";s:16:"content_picture1";s:13:"content_link1";s:13:"content_link1";s:16:"content_picture2";s:16:"content_picture2";s:13:"content_link2";s:13:"content_link2";s:16:"content_picture3";s:16:"content_picture3";s:13:"content_link3";s:13:"content_link3";s:16:"content_picture4";s:16:"content_picture4";s:13:"content_link4";s:13:"content_link4";s:16:"content_picture5";s:16:"content_picture5";s:13:"content_link5";s:13:"content_link5";s:3:"hit";s:3:"hit";}s:8:"key_name";a:24:{s:5:"ad_id";s:6:"编号";s:5:"title";s:6:"标题";s:5:"place";s:12:"显示位置";s:9:"is_active";s:12:"是否活动";s:11:"is_closable";s:15:"是否可关闭";s:5:"ctime";s:12:"创建时间";s:5:"mtime";s:12:"修改时间";s:13:"display_order";s:6:"顺序";s:12:"display_type";s:12:"广告类型";s:12:"content_html";s:6:"内容";s:12:"content_code";s:6:"内容";s:16:"content_picture0";s:6:"图片";s:13:"content_link0";s:6:"链接";s:16:"content_picture1";s:6:"图片";s:13:"content_link1";s:6:"链接";s:16:"content_picture2";s:6:"图片";s:13:"content_link2";s:6:"链接";s:16:"content_picture3";s:6:"图片";s:13:"content_link3";s:6:"链接";s:16:"content_picture4";s:6:"图片";s:13:"content_link4";s:6:"链接";s:16:"content_picture5";s:6:"图片";s:13:"content_link5";s:6:"链接";s:3:"hit";s:9:"点击量";}s:8:"key_type";a:24:{s:5:"ad_id";s:4:"word";s:5:"title";s:4:"text";s:5:"place";s:6:"select";s:9:"is_active";s:5:"radio";s:11:"is_closable";s:5:"radio";s:5:"ctime";s:4:"word";s:5:"mtime";s:4:"word";s:13:"display_order";s:4:"text";s:12:"display_type";s:5:"radio";s:12:"content_html";s:6:"editor";s:12:"content_code";s:8:"textarea";s:16:"content_picture0";s:5:"image";s:13:"content_link0";s:4:"text";s:16:"content_picture1";s:5:"image";s:13:"content_link1";s:4:"text";s:16:"content_picture2";s:5:"image";s:13:"content_link2";s:4:"text";s:16:"content_picture3";s:5:"image";s:13:"content_link3";s:4:"text";s:16:"content_picture4";s:5:"image";s:13:"content_link4";s:4:"text";s:16:"content_picture5";s:5:"image";s:13:"content_link5";s:4:"text";s:3:"hit";s:4:"text";}s:11:"key_default";a:24:{s:5:"ad_id";s:0:"";s:5:"title";s:0:"";s:5:"place";s:0:"";s:9:"is_active";s:0:"";s:11:"is_closable";s:0:"";s:5:"ctime";s:0:"";s:5:"mtime";s:0:"";s:13:"display_order";s:0:"";s:12:"display_type";s:0:"";s:12:"content_html";s:0:"";s:12:"content_code";s:0:"";s:16:"content_picture0";s:0:"";s:13:"content_link0";s:0:"";s:16:"content_picture1";s:0:"";s:13:"content_link1";s:0:"";s:16:"content_picture2";s:0:"";s:13:"content_link2";s:0:"";s:16:"content_picture3";s:0:"";s:13:"content_link3";s:0:"";s:16:"content_picture4";s:0:"";s:13:"content_link4";s:0:"";s:16:"content_picture5";s:0:"";s:13:"content_link5";s:0:"";s:3:"hit";s:0:"";}s:9:"key_tishi";a:24:{s:5:"ad_id";s:0:"";s:5:"title";s:0:"";s:5:"place";s:0:"";s:9:"is_active";s:0:"";s:11:"is_closable";s:0:"";s:5:"ctime";s:0:"";s:5:"mtime";s:0:"";s:13:"display_order";s:0:"";s:12:"display_type";s:0:"";s:12:"content_html";s:0:"";s:12:"content_code";s:0:"";s:16:"content_picture0";s:0:"";s:13:"content_link0";s:0:"";s:16:"content_picture1";s:0:"";s:13:"content_link1";s:0:"";s:16:"content_picture2";s:0:"";s:13:"content_link2";s:0:"";s:16:"content_picture3";s:0:"";s:13:"content_link3";s:0:"";s:16:"content_picture4";s:0:"";s:13:"content_link4";s:0:"";s:16:"content_picture5";s:0:"";s:13:"content_link5";s:0:"";s:3:"hit";s:0:"";}s:14:"key_javascript";a:24:{s:5:"ad_id";s:0:"";s:5:"title";s:0:"";s:5:"place";s:0:"";s:9:"is_active";s:0:"";s:11:"is_closable";s:0:"";s:5:"ctime";s:0:"";s:5:"mtime";s:0:"";s:13:"display_order";s:0:"";s:12:"display_type";s:0:"";s:12:"content_html";s:0:"";s:12:"content_code";s:0:"";s:16:"content_picture0";s:0:"";s:13:"content_link0";s:0:"";s:16:"content_picture1";s:0:"";s:13:"content_link1";s:0:"";s:16:"content_picture2";s:0:"";s:13:"content_link2";s:0:"";s:16:"content_picture3";s:0:"";s:13:"content_link3";s:0:"";s:16:"content_picture4";s:0:"";s:13:"content_link4";s:0:"";s:16:"content_picture5";s:0:"";s:13:"content_link5";s:0:"";s:3:"hit";s:0:"";}}', '2013-09-07 13:37:26'),
('pageKey', 'cat_Admin_info', 'a:4:{s:3:"key";a:7:{s:7:"info_id";s:7:"info_id";s:9:"entity_id";s:9:"entity_id";s:12:"entity_alias";s:12:"entity_alias";s:5:"value";s:5:"value";s:3:"top";s:3:"top";s:5:"recom";s:5:"recom";s:2:"do";s:2:"do";}s:8:"key_name";a:7:{s:7:"info_id";s:8:"信息ID";s:9:"entity_id";s:8:"实体ID";s:12:"entity_alias";s:12:"实体别名";s:5:"value";s:15:"首字段内容";s:3:"top";s:6:"置顶";s:5:"recom";s:6:"推荐";s:2:"do";s:6:"操作";}s:10:"key_hidden";a:7:{s:7:"info_id";s:1:"0";s:9:"entity_id";s:1:"0";s:12:"entity_alias";s:1:"0";s:5:"value";s:1:"0";s:3:"top";s:1:"0";s:5:"recom";s:1:"0";s:2:"do";s:1:"0";}s:14:"key_javascript";a:7:{s:7:"info_id";s:0:"";s:9:"entity_id";s:0:"";s:12:"entity_alias";s:0:"";s:5:"value";s:0:"";s:3:"top";s:0:"";s:5:"recom";s:0:"";s:2:"do";s:0:"";}}', '2013-09-07 16:11:24'),
('pageKey', 'cat_Admin_ads', 'a:4:{s:3:"key";a:25:{s:5:"ad_id";s:5:"ad_id";s:5:"title";s:5:"title";s:5:"place";s:5:"place";s:9:"is_active";s:9:"is_active";s:11:"is_closable";s:11:"is_closable";s:5:"ctime";s:5:"ctime";s:5:"mtime";s:5:"mtime";s:13:"display_order";s:13:"display_order";s:12:"display_type";s:12:"display_type";s:12:"content_html";s:12:"content_html";s:12:"content_code";s:12:"content_code";s:16:"content_picture0";s:16:"content_picture0";s:13:"content_link0";s:13:"content_link0";s:16:"content_picture1";s:16:"content_picture1";s:13:"content_link1";s:13:"content_link1";s:16:"content_picture2";s:16:"content_picture2";s:13:"content_link2";s:13:"content_link2";s:16:"content_picture3";s:16:"content_picture3";s:13:"content_link3";s:13:"content_link3";s:16:"content_picture4";s:16:"content_picture4";s:13:"content_link4";s:13:"content_link4";s:16:"content_picture5";s:16:"content_picture5";s:13:"content_link5";s:13:"content_link5";s:3:"hit";s:3:"hit";s:8:"DOACTION";s:8:"DOACTION";}s:8:"key_name";a:25:{s:5:"ad_id";s:8:"广告ID";s:5:"title";s:6:"标题";s:5:"place";s:6:"位置";s:9:"is_active";s:12:"是否可用";s:11:"is_closable";s:12:"可否关闭";s:5:"ctime";s:12:"创建时间";s:5:"mtime";s:12:"修改时间";s:13:"display_order";s:12:"显示顺序";s:12:"display_type";s:12:"显示类型";s:12:"content_html";s:4:"html";s:12:"content_code";s:6:"代码";s:16:"content_picture0";s:7:"图片1";s:13:"content_link0";s:13:"图片1链接";s:16:"content_picture1";s:7:"图片2";s:13:"content_link1";s:13:"图片2链接";s:16:"content_picture2";s:7:"图片3";s:13:"content_link2";s:13:"图片3链接";s:16:"content_picture3";s:7:"图片4";s:13:"content_link3";s:13:"图片4链接";s:16:"content_picture4";s:7:"图片5";s:13:"content_link4";s:13:"图片5链接";s:16:"content_picture5";s:7:"图片6";s:13:"content_link5";s:13:"图片6链接";s:3:"hit";s:9:"点击量";s:8:"DOACTION";s:6:"操作";}s:10:"key_hidden";a:25:{s:5:"ad_id";s:1:"0";s:5:"title";s:1:"0";s:5:"place";s:1:"0";s:9:"is_active";s:1:"0";s:11:"is_closable";s:1:"0";s:5:"ctime";s:1:"0";s:5:"mtime";s:1:"0";s:13:"display_order";s:1:"0";s:12:"display_type";s:1:"0";s:12:"content_html";s:1:"0";s:12:"content_code";s:1:"0";s:16:"content_picture0";s:1:"0";s:13:"content_link0";s:1:"0";s:16:"content_picture1";s:1:"0";s:13:"content_link1";s:1:"0";s:16:"content_picture2";s:1:"0";s:13:"content_link2";s:1:"0";s:16:"content_picture3";s:1:"0";s:13:"content_link3";s:1:"0";s:16:"content_picture4";s:1:"0";s:13:"content_link4";s:1:"0";s:16:"content_picture5";s:1:"0";s:13:"content_link5";s:1:"0";s:3:"hit";s:1:"0";s:8:"DOACTION";s:1:"0";}s:14:"key_javascript";a:25:{s:5:"ad_id";s:0:"";s:5:"title";s:0:"";s:5:"place";s:0:"";s:9:"is_active";s:0:"";s:11:"is_closable";s:0:"";s:5:"ctime";s:0:"";s:5:"mtime";s:0:"";s:13:"display_order";s:0:"";s:12:"display_type";s:0:"";s:12:"content_html";s:0:"";s:12:"content_code";s:0:"";s:16:"content_picture0";s:0:"";s:13:"content_link0";s:0:"";s:16:"content_picture1";s:0:"";s:13:"content_link1";s:0:"";s:16:"content_picture2";s:0:"";s:13:"content_link2";s:0:"";s:16:"content_picture3";s:0:"";s:13:"content_link3";s:0:"";s:16:"content_picture4";s:0:"";s:13:"content_link4";s:0:"";s:16:"content_picture5";s:0:"";s:13:"content_link5";s:0:"";s:3:"hit";s:0:"";s:8:"DOACTION";s:0:"";}}', '2013-09-10 08:41:54'),
('pageKey', 'cat_Admin_addEntity', 'a:6:{s:3:"key";a:25:{s:9:"entity_id";s:9:"entity_id";s:4:"name";s:4:"name";s:5:"alias";s:5:"alias";s:8:"show_nav";s:8:"show_nav";s:9:"show_post";s:9:"show_post";s:7:"pb_icon";s:7:"pb_icon";s:10:"show_index";s:10:"show_index";s:11:"need_active";s:11:"need_active";s:7:"can_rec";s:7:"can_rec";s:10:"rec_entity";s:10:"rec_entity";s:4:"sort";s:4:"sort";s:3:"cat";s:3:"cat";s:12:"can_post_gid";s:12:"can_post_gid";s:12:"can_read_gid";s:12:"can_read_gid";s:8:"can_over";s:8:"can_over";s:10:"use_detail";s:10:"use_detail";s:10:"tpl_detail";s:10:"tpl_detail";s:8:"use_list";s:8:"use_list";s:8:"tpl_list";s:8:"tpl_list";s:4:"tpl1";s:4:"tpl1";s:4:"tpl2";s:4:"tpl2";s:4:"tpl3";s:4:"tpl3";s:4:"des1";s:4:"des1";s:4:"des2";s:4:"des2";s:4:"des3";s:4:"des3";}s:8:"key_name";a:25:{s:9:"entity_id";s:2:"ID";s:4:"name";s:9:"实体名";s:5:"alias";s:12:"实体别名";s:8:"show_nav";s:18:"显示在导航中";s:9:"show_post";s:18:"显示发布按钮";s:7:"pb_icon";s:18:"发布按钮图标";s:10:"show_index";s:15:"在首页显示";s:11:"need_active";s:12:"需要审核";s:7:"can_rec";s:18:"能够接收信息";s:10:"rec_entity";s:18:"接收实体类型";s:4:"sort";s:6:"排序";s:3:"cat";s:12:"分类目录";s:12:"can_post_gid";s:35:"允许发布该实体的用户组id";s:12:"can_read_gid";s:35:"允许阅读该实体的用户组id";s:8:"can_over";s:30:"是否允许设置截止日期";s:10:"use_detail";s:18:"使用详情模板";s:10:"tpl_detail";s:12:"详情模板";s:8:"use_list";s:18:"使用列表模板";s:8:"tpl_list";s:12:"列表模板";s:4:"tpl1";s:16:"自定义模板1";s:4:"tpl2";s:16:"自定义模板2";s:4:"tpl3";s:16:"自定义模板3";s:4:"des1";s:30:"发布提示（发布页面）";s:4:"des2";s:30:"温馨提示（详情页面）";s:4:"des3";s:16:"自定义描述3";}s:8:"key_type";a:25:{s:9:"entity_id";s:4:"word";s:4:"name";s:4:"text";s:5:"alias";s:4:"text";s:8:"show_nav";s:6:"select";s:9:"show_post";s:6:"select";s:7:"pb_icon";s:5:"image";s:10:"show_index";s:6:"select";s:11:"need_active";s:6:"select";s:7:"can_rec";s:6:"select";s:10:"rec_entity";s:10:"stringText";s:4:"sort";s:4:"text";s:3:"cat";s:6:"select";s:12:"can_post_gid";s:10:"stringText";s:12:"can_read_gid";s:10:"stringText";s:8:"can_over";s:6:"select";s:10:"use_detail";s:6:"select";s:10:"tpl_detail";s:8:"textarea";s:8:"use_list";s:6:"select";s:8:"tpl_list";s:8:"textarea";s:4:"tpl1";s:8:"textarea";s:4:"tpl2";s:8:"textarea";s:4:"tpl3";s:8:"textarea";s:4:"des1";s:8:"textarea";s:4:"des2";s:8:"textarea";s:4:"des3";s:8:"textarea";}s:11:"key_default";a:25:{s:9:"entity_id";s:0:"";s:4:"name";s:0:"";s:5:"alias";s:0:"";s:8:"show_nav";s:1:"1";s:9:"show_post";s:1:"1";s:7:"pb_icon";s:0:"";s:10:"show_index";s:1:"1";s:11:"need_active";s:0:"";s:7:"can_rec";s:0:"";s:10:"rec_entity";s:0:"";s:4:"sort";s:0:"";s:3:"cat";s:0:"";s:12:"can_post_gid";s:3:"1,3";s:12:"can_read_gid";s:3:"1,3";s:8:"can_over";s:0:"";s:10:"use_detail";s:2:"-1";s:10:"tpl_detail";s:0:"";s:8:"use_list";s:2:"-1";s:8:"tpl_list";s:0:"";s:4:"tpl1";s:0:"";s:4:"tpl2";s:0:"";s:4:"tpl3";s:0:"";s:4:"des1";s:0:"";s:4:"des2";s:0:"";s:4:"des3";s:0:"";}s:9:"key_tishi";a:25:{s:9:"entity_id";s:0:"";s:4:"name";s:27:"仅限英文，用于识别";s:5:"alias";s:30:"用于显示在前台的名称";s:8:"show_nav";s:27:"是否显示在导航栏中";s:9:"show_post";s:24:"是否显示发布按钮";s:7:"pb_icon";s:0:"";s:10:"show_index";s:37:"是否在首页显示最新5条记录";s:11:"need_active";s:0:"";s:7:"can_rec";s:0:"";s:10:"rec_entity";s:0:"";s:4:"sort";s:45:"影响到在首页和导航栏的显示顺序";s:3:"cat";s:0:"";s:12:"can_post_gid";s:207:"输入后回车，0和不输入为所有用户。常用系统默认用户组ID： 1-管理员；2-巡逻员；3-正常用户；4-禁言用户；5-个人认证用户；6-企业认证用户；7-达人用户。";s:12:"can_read_gid";s:207:"输入后回车，0和不输入为所有用户。常用系统默认用户组ID： 1-管理员；2-巡逻员；3-正常用户；4-禁言用户；5-个人认证用户；6-企业认证用户；7-达人用户。";s:8:"can_over";s:45:"允许则可以设置到期后隐藏的字段";s:10:"use_detail";s:0:"";s:10:"tpl_detail";s:0:"";s:8:"use_list";s:0:"";s:8:"tpl_list";s:0:"";s:4:"tpl1";s:42:"选填，用于用户自定义显示样式";s:4:"tpl2";s:36:"选填，用于用户自定义样式";s:4:"tpl3";s:36:"选填，用于用户自定义样式";s:4:"des1";s:79:"选填，用于提示，模板调用，支持html代码，严禁使用单引号";s:4:"des2";s:79:"选填，用于提示，模板调用，支持html代码，严禁使用单引号";s:4:"des3";s:36:"选填，用于提示，模板调用";}s:14:"key_javascript";a:25:{s:9:"entity_id";s:0:"";s:4:"name";s:0:"";s:5:"alias";s:0:"";s:8:"show_nav";s:0:"";s:9:"show_post";s:0:"";s:7:"pb_icon";s:0:"";s:10:"show_index";s:0:"";s:11:"need_active";s:0:"";s:7:"can_rec";s:0:"";s:10:"rec_entity";s:0:"";s:4:"sort";s:0:"";s:3:"cat";s:0:"";s:12:"can_post_gid";s:0:"";s:12:"can_read_gid";s:0:"";s:8:"can_over";s:0:"";s:10:"use_detail";s:0:"";s:10:"tpl_detail";s:0:"";s:8:"use_list";s:0:"";s:8:"tpl_list";s:0:"";s:4:"tpl1";s:0:"";s:4:"tpl2";s:0:"";s:4:"tpl3";s:0:"";s:4:"des1";s:0:"";s:4:"des2";s:0:"";s:4:"des3";s:0:"";}}', '2013-10-21 05:20:04');


INSERT INTO `ts_system_data` ( `list`, `key`, `value`, `mtime`) VALUES
('cat_Admin', 'index', 'a:2:{s:3:"CSS";s:1870:".cat_des {\r\n    line-height: 24px;\r\n    color: #333;\r\n    font-size: 14px;\r\n    width: 500px;\r\n    float: left;\r\n    height:100px ;\r\n    overflow: hidden;\r\n}\r\n.cat_head_pic{\r\n    float: right;\r\n}\r\n.cat_uname {\r\n    text-align: center;\r\n    line-height: 28px;\r\n    color: #648B9D;\r\n}\r\n.cat_uname a{\r\n    color: #648B9D;\r\n}\r\n.cat_head_size{\r\n    width: 100px;\r\n    height: 100px;\r\n}\r\n.cat_ul_tm {\r\n    font-size: 12px;\r\n    color: #648B9D;\r\n}\r\n.cat_yunme_title {\r\n    font-size: 24px;\r\n    font-weight: normal;\r\n}\r\n\r\n.cat_yunmai_det{\r\n    border: solid 1px #ccc;\r\n    padding: 10px;\r\n    margin-top: 20px;\r\n    background-color: #FBFBFB;\r\n}\r\n.cat_yunmai_det li{\r\n    float:left;\r\n    width: 270px;\r\n    line-height: 28px;\r\n    font-size: 14px;\r\n}\r\n.cat_yunmai_title{\r\n    font-size: 20px;\r\n    font-weight: normal;\r\n    margin-top: 10px;\r\n    margin-bottom: 10px;\r\n}\r\n.cat_yunmai_des{\r\n    line-height: 1.5;\r\n    font-size: 16px;\r\n    text-indent: 20px;\r\n\r\n}\r\n.cat_yunmai_d_left{\r\n    border-right: solid 1px #ccc;\r\n    min-height: 500px;\r\n}\r\n/*58*/\r\n.fe_main:hover {\r\n    background-color: #FFFEE5;\r\n}\r\n.fe_main {\r\n\r\n    border-bottom: solid 1px #ccc;\r\n}\r\n\r\n.fe_m {\r\n    width: 86px;\r\n}\r\n\r\n.fe_time {\r\n    font-size: 12px;\r\n    line-height: 75px;\r\n}\r\n\r\n.fe_detail {\r\n    width: 300px;\r\n\r\n}\r\n\r\n.fe_det {\r\n    font-size: 12px;\r\n    margin-top: 10px;\r\n\r\n}\r\n\r\n.fe_money {\r\n    color: #E22;\r\n    font-size: 16px;\r\n    font-style: normal;\r\n    font-weight: bold;\r\n    font-family: "微软雅黑";\r\n}\r\n\r\n.pic1 {\r\n    width: 100px;\r\n    height: 75px;\r\n}\r\n\r\n.left {\r\n    float: left;\r\n}\r\n\r\n.mg10 {\r\n    margin: 10px;\r\n}\r\n\r\n.fe_p {\r\n    color: #666;\r\n    font-size: 12px;\r\n    max-height: 45px;\r\n    overflow: hidden;\r\n    line-height:16px;\r\n}\r\n\r\n.fe_title {\r\n    max-height: 45px;\r\n    overflow: hidden;\r\n    font-size: 16px;\r\n    font-family: "微软雅黑";\r\n    color: #25D;\r\n}";s:2:"JS";s:0:"";}', '2013-07-14 10:50:47');


INSERT INTO `ts_notify_node` ( `node`, `nodeinfo`, `appname`, `content_key`, `title_key`, `send_email`, `send_message`, `type`) VALUES
( 'mxcat_atme', '@消息', 'cat', 'NOTIFY_MXCAT_ATME_CONTENT', 'NOTIFY_MXCAT_ATME_TITLE', 0, 1, 1);


INSERT INTO `ts_lang` ( `key`, `appname`, `filetype`, `zh-cn`, `en`, `zh-tw`) VALUES
( 'NOTIFY_MXCAT_ATME_CONTENT', 'CAT', 0, '您的好友 <a href="{space_url}" target=''_blank'' style="text-decoration:none;color:#3366cc;">{name}</a> 刚刚在以下内容中提到了你：<br /><table style="width:480px;background:#eee;padding:10px"><tbody><tr><td style="width:50px;float:left;margin-right:10px"><img src="{face}"></td><td style="font-size:12px;width:420px"><div style="padding:0;margin:0"><a href="{space_url}" target=''_blank'' style="text-decoration:none;color:#3366cc;">{name}</a>:</div><div style="padding:0;margin:0">{content}</div><div style="padding:0;margin:0"><span style="float:right"><a href="{feed_url}" style="text-decoration:none;color:#3366cc">收藏</a>｜<a href="{feed_url}" style="text-decoration:none;color:#3366cc">转发</a>｜<a href="{feed_url}" style="text-decoration:none;color:#3366cc">评论</a></span><span>{publish_time}   来自  分类信息</span></div></td></tr></tbody></table><br />\n\n', 'Your colleagues {name} just mentioned you in the following content: {content}.<a href="{site_url}" target="_blank">Go to the website>></a>\n\n', '您的好友 {name} 剛剛在以下內容中提到了你：{content}。<a href="{site_url}" target="_blank">去網站看看>></a>\n\n'),
( 'NOTIFY_MXCAT_ATME_TITLE', 'CAT', 0, '[ {site} ]{name}刚刚提到了您', '[{site}] {name} just mentioned you\n\n', '[ {site} ] {name} 剛剛提到了您\n\n');



