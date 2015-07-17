<?php
if (!defined('SITE_PATH')) exit();
$db_prefix = C('DB_PREFIX');
$sql = array(
    "DROP TABLE IF EXISTS `{$db_prefix}cat`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_com`;",

    "DROP TABLE IF EXISTS `{$db_prefix}cat_data`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_entity`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_fav`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_field`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_info`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_rate`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_read`;",
    "DROP TABLE IF EXISTS `{$db_prefix}cat_send`;",
    "DROP TABLE IF EXISTS `{$db_prefix}adspace_ad`;",
    "DROP TABLE IF EXISTS `{$db_prefix}adspace_place`;",
    "DELETE FROM `{$db_prefix}system_config` WHERE `key` like 'cat_Admin_%';",
    "DELETE FROM `{$db_prefix}system_data` WHERE `list` = 'cat_Admin';",
    "DELETE FROM `{$db_prefix}lang` WHERE `key` like 'NOTIFY_MXCAT_ATME_%'",
    "DELETE FROM `{$db_prefix}notify_node` WHERE `node` LIKE 'mxcat_atme'",

);
foreach ($sql as $v) {
    D('')->execute($v);
}