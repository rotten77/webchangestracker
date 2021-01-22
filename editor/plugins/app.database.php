<?php
class AppDatabase {

	function credentials() {
        return array(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
    }
	
	function database() {
		return MYSQL_DB;
	}

	function login() {
		return true;
	}
}