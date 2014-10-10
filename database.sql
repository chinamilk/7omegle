-- 用户表
CREATE TABLE IF NOT EXISTS `user`(
	`id` int(11) unsigned NOT NULL auto_increment,
	`username` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
	`email` varchar(130) NOT NULL DEFAULT '',
	`is_activate` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已验证邮件并激活; 0: 未激活, 1: 已激活',
	`password` varchar(130) NOT NULL DEFAULT '',
	`cookie_hash` varchar(130) NOT NULL DEFAULT '' COMMENT 'cookie hash值',
	`user_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用户类型, 0: 普通用户, 1: 管理员',
	`is_blocked` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用户是否被锁定, 0: 未锁定, 1: 已锁定',
	`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	UNIQUE KEY `user_username_unique` (`username`),
	UNIQUE KEY `user_email_unique` (`email`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- 用户资料表
CREATE TABLE IF NOT EXISTS `profile`(
	`user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户id',
	-- `avatar_origin` varchar(130) NOT NULL DEFAULT '' COMMENT '原始头像文件',
	`avatar_medium` varchar(130) NOT NULL DEFAULT '' COMMENT '用户中等头像文件名',
	`avatar_small` varchar(130) NOT NULL DEFAULT '' COMMENT '用户小头像文件名',
	`gender` varchar(10) NOT NULL DEFAULT '' COMMENT '性别, 用户随便填写文字',
	`location` varchar(130) NOT NULL DEFAULT '' COMMENT '居住地',
	`tagline` varchar(130) NOT NULL DEFAULT '' COMMENT '签名',
	`bio` text NOT NULL DEFAULT '' COMMENT '个人简介',
	`status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '忙碌状态, 0: 空闲,可匹配; 1: 忙碌,不可匹配; 2: 拒绝匹配,由于个人原因设置为此',
	`last_online_time` datetime NOT NULL DEFAULT 0 COMMENT '最后在线时间，总是通过ajax更新最后在线时间; 匹配好友或统计在线时用',
	UNIQUE KEY `profile_user_id` (`user_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- -- 兴趣标签类别
-- CREATE TABLE IF NOT EXISTS `interest_tag`(
-- 	`id` int(11) unsigned NOT NULL DEFAULT 0,
-- 	`content` varchar(30) NOT NULL DEFAULT '' COMMENT '兴趣标签的具体内容',
-- 	`count` int(11) NOT NULL DEFAULT 0 COMMENT '对此标签感兴趣的总人数',
-- 	UNIQUE KEY `interest_tag_content` (`content`)
-- ) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- -- 个人兴趣标签
-- CREATE TABLE IF NOT EXISTS `user_interest_tag`(
-- 	`id` int(11) unsigned NOT NULL DEFAULT 0,
-- 	`user_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
-- 	`interest_tag_id` int(11) NOT NULL DEFAULT 0 COMMENT '兴趣标签id',
-- 	PRIMARY KEY (`id`),
-- 	INDEX `user_interest_tag_user_id` (`user_id`)
-- ) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- 好友表
CREATE TABLE IF NOT EXISTS `user_friend`(
	`id` int(11) unsigned NOT NULL auto_increment,
	`user_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
	`friend_id` int(11) NOT NULL DEFAULT 0 COMMENT '好友id',
	`is_useless` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否已经无效; 0: 仍然有效; 1: 已经无效; 对方超过2天没有任何动态则可以申请重新分配好友',
	`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '成为好友的日期, 最近7天的好友根据此时间计算',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- 动态
CREATE TABLE IF NOT EXISTS `feed`(
	`id` int(11) unsigned NOT NULL auto_increment,
	`user_id` int(11) NOT NULL DEFAULT 0 COMMENT '发布动态的用户id',
	`user_friend_id` int(11) NOT NULL DEFAULT 0 COMMENT '好友表中的id, 外键',
	`content` text NOT NULL DEFAULT '' COMMENT '动态内容',
	`image` varchar(130) NOT NULL DEFAULT '' COMMENT '动态中的图片文件名(待定: 如果有多幅用<,>分割)',
	`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '动态发表时间',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- 链接验证码相关, 比如注册激活邮箱链接验证, 忘记密码链接验证
CREATE TABLE IF NOT EXISTS `validate`(
	`id` int(11) unsigned NOT NULL auto_increment,
	`type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '验证码类型, 1: 注册激活验证; 2: 忘记密码验证',
	`code` varchar(130) NOT NULL DEFAULT '' COMMENT '验证码',
	`is_used` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已使用, 0: 未使用, 1: 已使用',
	`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '生成时间',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- 反馈
CREATE TABLE IF NOT EXISTS `feedback`(
	`id` int(11) unsigned NOT NULL auto_increment,
	`contact` varchar(130) NOT NULL DEFAULT '' COMMENT '联系方式',
	`content` text NOT NULL DEFAULT '' COMMENT '反馈内容',
	`ip` varchar(30) NOT NULL DEFAULT '',
	`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

-- 登录日志
CREATE TABLE IF NOT EXISTS `login_log`(
	`id` int(11) unsigned NOT NULL auto_increment,
	`user_id` int(11) unsigned NOT NULL DEFAULT 0,
	`ip` varchar(30) NOT NULL DEFAULT '',
	`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `login_log_user_id_index` (`user_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;
