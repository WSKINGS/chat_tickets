-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 01 月 21 日 15:46
-- 服务器版本: 5.1.49
-- PHP 版本: 5.2.12

drop table if exists end_chat_push;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `andriod_push`
--

-- --------------------------------------------------------

--
-- 表的结构 `zxzbcar_push`
--

CREATE TABLE IF NOT EXISTS `end_chat_push` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `sendno` int(64) NOT NULL DEFAULT '1',
  `n_title` varchar(120) DEFAULT NULL,
  `n_content` char(120) NOT NULL,
  `errcode` char(64) NOT NULL,
  `errmsg` varchar(255) NOT NULL,
  `total_user` int(20) NOT NULL,
  `send_cnt` int(25) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Store unique devices' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `zxzbcar_push`
--

INSERT INTO `end_chat_push` (`id`, `sendno`, `n_title`, `n_content`, `errcode`, `errmsg`, `total_user`, `send_cnt`, `created`) VALUES
(1, 1, '你好！', '你好！欢迎使用分合科技APP!', '0', '发送成功', 0, 0, '2013-06-21 15:41:35');
