<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$sql =	"CREATE TABLE " .KNEWS_USERS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		email varchar(100) NOT NULL,
		state varchar(2) NOT NULL,
		joined datetime NOT NULL,
		confkey varchar(32),
		lang varchar(12) NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_EXTRA_FIELDS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		show_table tinyint(1) NOT NULL DEFAULT '0',
		token varchar(100) NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;
	   
		INSERT INTO " .KNEWS_EXTRA_FIELDS . " (id, name, show_table, token) VALUES 
		(1, 'name', 1, '%name%'),
		(2, 'surname', 0, '%surname%');";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_USERS_EXTRA . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id bigint(20) UNSIGNED NOT NULL,
		field_id bigint(20) UNSIGNED NOT NULL,
		value mediumtext NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_LISTS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		open varchar(1) NOT NULL,
		open_registered varchar(1) NOT NULL,
		langs varchar(100) NOT NULL,
		orderlist int(11) NOT NULL DEFAULT '0',
		UNIQUE KEY id (id)
	   )$charset_collate;
	   
		INSERT INTO " .KNEWS_LISTS . " (id, name, open, open_registered, langs, orderlist) VALUES 
		(1, 'Default list', 1, 1, '', 0);";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_USERS_PER_LISTS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		id_user bigint(20) UNSIGNED NOT NULL,
		id_list bigint(20) UNSIGNED NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_NEWSLETTERS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		subject varchar(100) NOT NULL,
		created datetime NOT NULL,
		modified datetime NOT NULL,
		template varchar(100) NOT NULL,
		html_mailing mediumtext NOT NULL,
		html_modules mediumtext NOT NULL,
		html_container mediumtext NOT NULL,
		html_head mediumtext NOT NULL,
		lang varchar(12) NOT NULL DEFAULT '', 
		automated varchar(1) NOT NULL DEFAULT 0,

		mobile varchar(1) NOT NULL DEFAULT 0,
		id_mobile bigint(20) UNSIGNED NOT NULL DEFAULT 0,

		UNIQUE KEY id (id)
	   )$charset_collate;";

dbDelta($sql);

if (!$this->tableExists(KNEWS_NEWSLETTERS_SUBMITS)) {

	$sql =	"CREATE TABLE " .KNEWS_NEWSLETTERS_SUBMITS . " (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) UNSIGNED NOT NULL DEFAULT " . $this->KNEWS_MAIN_BLOG_ID . ",
			newsletter int(11) NOT NULL,
			finished tinyint(1) NOT NULL,
			paused tinyint(1) NOT NULL,
			start_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			end_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			users_total int(11) NOT NULL,
			users_ok int(11) NOT NULL,
			users_error int(11) NOT NULL,
			priority tinyint(4) NOT NULL,
			strict_control varchar(100) NOT NULL,
			emails_at_once int(11) NOT NULL,
			special varchar(32) NOT NULL,
			UNIQUE KEY id (id)
		   )$charset_collate;";
	
	dbDelta($sql);
}

$sql =	"CREATE TABLE " .KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		submit bigint(20) UNSIGNED NOT NULL,
		user bigint(20) UNSIGNED NOT NULL,
		status TINYINT NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_STATS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id bigint(20) UNSIGNED NOT NULL,
		submit_id bigint(20) UNSIGNED NOT NULL,
		what int(2) NOT NULL,
		date datetime NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_KEYS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		keyy varchar(32) NOT NULL,
		type int(2) NOT NULL,
		submit_id bigint(20) UNSIGNED NOT NULL,
		href mediumtext NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";

dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_AUTOMATED . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		selection_method varchar(1) NOT NULL,
		target_id bigint(20) UNSIGNED NOT NULL,
		newsletter_id bigint(20) UNSIGNED NOT NULL,
		lang varchar(12) NOT NULL,
		paused varchar(1) NOT NULL,
		auto varchar(1) NOT NULL,
		every_mode int(11) NOT NULL,
		every_time int(11) NOT NULL,
		what_dayweek int(11) NOT NULL,
		every_posts int(11) NOT NULL,
		last_run datetime NOT NULL,
		emails_at_once int(11) NOT NULL DEFAULT 25,
		UNIQUE KEY id (id)
	   )$charset_collate;";
	   
dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_AUTOMATED_POSTS . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		id_automated bigint(20) UNSIGNED NOT NULL,
		id_post bigint(20) UNSIGNED NOT NULL,
		id_news bigint(20) UNSIGNED NOT NULL,
		when_scheduled datetime NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";
	   
dbDelta($sql);

$sql =	"CREATE TABLE " .KNEWS_AUTOMATED_SELECTION . " (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		id_automated bigint(20) UNSIGNED NOT NULL,
		type varchar(100) NOT NULL,
		value varchar(100) NOT NULL,
		UNIQUE KEY id (id)
	   )$charset_collate;";
	   
dbDelta($sql);

update_option('knews_version', KNEWS_VERSION);
?>