<?php
class AppDatabase {

	function credentials() {
        return array(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
    }
	
	function database() {
		return MYSQL_DB;
	}

	function login($login, $password) {
		return (bool) ($login == EMAIL_ADDRESS && $password == PASSWORD);
	}
}