<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ziss_cls_dbquery {

	public static function ziss_count($id = 0) {
	
		global $wpdb;
		$result = '0';
		
		if($id <> "" && $id > 0) {
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "zoom_image_simples WHERE ziss_id = %d", array($id));
		} 
		else {
			$sSql = "SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "zoom_image_simples";
		}
		
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function ziss_select_bygroup($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "zoom_image_simples";

		if($group <> "") {
			$sSql = $sSql . " WHERE ziss_group = %s order by ziss_id desc";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " order by ziss_id desc";
		}

		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function ziss_select_byid($id = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "zoom_image_simples";

		if($id <> "") {
			$sSql = $sSql . " WHERE ziss_id = %d LIMIT 1";
			$sSql = $wpdb->prepare($sSql, array($id));
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else {
			$sSql = $sSql . " order by ziss_group, ziss_order";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		
		return $arrRes;
	}
	
	public static function ziss_select_bygroup_rand($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "zoom_image_simples";

		if($group <> "") {
			$sSql = $sSql . " WHERE ziss_group = %s order by rand() LIMIT 1";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " order by rand() LIMIT 1";
		}

		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function ziss_select_shortcode($id = "", $group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "zoom_image_simples WHERE ziss_status = 'Yes'";
		//$sSql .= " AND ( ziss_start <= NOW() or ziss_start = '0000-00-00' )";
		//$sSql .= " AND ( ziss_end >= NOW() or ziss_end = '0000-00-00' )";
		
		if($id <> "" && $id <> "0") {
			$sSql .= " AND ziss_id = %d LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($id));
		}
		elseif($group <> "") {
			$sSql .= " AND ziss_group = %s Order by rand() LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
		}
		
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		
		return $arrRes;
	}
	
	public static function ziss_group() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT distinct(ziss_group) FROM " . $wpdb->prefix . "zoom_image_simples order by ziss_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}

	public static function ziss_delete($id = "") {

		global $wpdb;

		if($id <> "") {
			$sSql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "zoom_image_simples WHERE ziss_id = %s LIMIT 1", $id);
			$wpdb->query($sSql);
		}
		
		return true;
	}

	public static function ziss_action_ins($data = array(), $action = "insert") {

		global $wpdb;
		
		if($action == "insert") {
			$sql = $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "zoom_image_simples
				(ziss_title, ziss_img_sm, ziss_img_bg, ziss_width, ziss_height, ziss_fade, ziss_scale, ziss_position, ziss_group, ziss_status) VALUES 
				(%s, %s, %s, %d, %d, %d, %d, %s, %s, %s)", 
				array($data["ziss_title"], $data["ziss_img_sm"], $data["ziss_img_bg"], $data["ziss_width"], $data["ziss_height"], $data["ziss_fade"], 
				$data["ziss_scale"], $data["ziss_position"], $data["ziss_group"], $data["ziss_status"]));
			$wpdb->query($sql);
			return "inserted";
		}
		elseif($action == "update") {
			$sSql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "zoom_image_simples SET ziss_title = %s, ziss_img_sm = %s, ziss_img_bg = %s, 
				ziss_width = %d, ziss_height = %d, ziss_fade = %d, ziss_scale = %d, ziss_position = %s, ziss_group = %s, ziss_status = %s WHERE ziss_id = %d LIMIT 1", 
				array($data["ziss_title"], $data["ziss_img_sm"], $data["ziss_img_bg"], $data["ziss_width"], $data["ziss_height"], $data["ziss_fade"], 
				$data["ziss_scale"], $data["ziss_position"], $data["ziss_group"], $data["ziss_status"], $data["ziss_id"]));
			$wpdb->query($sSql);
			return "update";
		}
	}
	
	public static function ziss_default() {

		$count = ziss_cls_dbquery::ziss_count($id = 0);
		if($count == 0){
			$folderpath = plugin_dir_url( __DIR__ );
			if (ziss_cls_dbquery::ziss_endswith($folderpath, '/') == false) {
				$folderpath = $folderpath . "/";
			}
			
			$sing_sm_1 = $folderpath . 'sample/sing_sm_1.jpg';
			$sing_bg_1 = $folderpath . 'sample/sing_bg_1.jpg';
		
			$data['ziss_title'] = 'Sample default image 1';
			$data['ziss_img_sm'] = $sing_sm_1;
			$data['ziss_img_bg'] = $sing_bg_1;
			$data['ziss_width'] = '0';
			$data['ziss_height'] = '0';
			$data['ziss_fade'] = '500';
			$data['ziss_scale'] = '0';
			$data['ziss_position'] = '';
			$data['ziss_group'] = 'Group1';
			$data['ziss_status'] = 'Yes';
			ziss_cls_dbquery::ziss_action_ins($data, "insert");
			
			$sing_sm_1 = $folderpath . 'sample/sing_sm_2.jpg';
			$sing_bg_1 = $folderpath . 'sample/sing_bg_2.jpg';
			$data['ziss_title'] = 'Sample default image 2';
			$data['ziss_img_sm'] = $sing_sm_1;
			$data['ziss_img_bg'] = $sing_bg_1;
			ziss_cls_dbquery::ziss_action_ins($data, "insert");
		}
	}
	
	public static function ziss_common_text($value) {
		
		$returnstring = "";
		switch ($value) 
		{
			case "Yes":
				$returnstring = '<span style="color:#006600;">Yes</span>';
				break;
			case "No":
				$returnstring = '<span style="color:#FF0000;">No</span>';
				break;
			case "_blank":
				$returnstring = '<span style="color:#006600;">New window</span>';
				break;
			case "_self":
				$returnstring = '<span style="color:#0000FF;">Same window</span>';
				break;
			default:
       			$returnstring = $value;
		}
		return $returnstring;
	}
	
	public static function ziss_endswith($fullstr, $needle)
    {
        $strlen = strlen($needle);
        $fullstrend = substr($fullstr, strlen($fullstr) - $strlen);
        return $fullstrend == $needle;
    }
}