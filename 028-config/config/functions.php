<?php
/*
 * Description: diagnosis program funxtions
 * Referring to the 
 * Author: Wangbin
*/
	/**
	* ファンクション名：ps_url_exists
	* 機能概要：URLのありなし確認
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $url
	* @return Boolean true/false
	*/
	if ( ! function_exists('ps_url_exists')){
		function ps_url_exists($url) {
		    if (!$fp = curl_init($url)) return false;
		    return true;
		}	
	}
	
	
?>
