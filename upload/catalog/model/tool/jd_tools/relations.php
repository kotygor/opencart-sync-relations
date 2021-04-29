<?php
class ModelToolJDToolsRelations extends Model {
	public function getOldRelationKey($key, $value, $source) {
		$sql = "SELECT `old_value` FROM `" . DB_PREFIX . "relations` WHERE `key` = '" . $key . "' AND `new_value` = '" . $value . "' AND `data_source` = '" . $source . "'";
		$result = $this->db->query($sql);

		if(!empty($result->row['old_value'])) return $result->row['old_value'];
		else return false;
	}
	public function getNewRelationKey($key, $value, $source) {
		$sql = "SELECT `new_value` FROM `" . DB_PREFIX . "relations` WHERE `key` = '" . $key . "' AND `old_value` = '" . $value . "' AND `data_source` = '" . $source . "'";
		$result = $this->db->query($sql);

		if(!empty($result->row['new_value'])) return $result->row['new_value'];
		else return false;
	}
	public function setRelationKey( $key, $old_value, $new_value, $source, $lastmode = '') {
		$sql = "INSERT `" . DB_PREFIX. "relations` (`key`, `old_value`, `new_value`, `data_source`, `lastmode`) VALUE ('" . $key ."', '". $old_value ."', '". $new_value ."', '" . $source . "', " . (($lastmode == '')? 'NOW()' : "'" . $lastmode . "'" ) . ");";
		$result = $this->db->query($sql);
		return $result;
	}
}