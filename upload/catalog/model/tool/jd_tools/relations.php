<?php
// v0.1.4
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

	/**
	 * @param $key, ключ-ід
	 * @param $source, джерело
	 * @param string[] $fields, пара ключ-значення
	 * @return array
	 */
	public function getExistingRelations($key, $source, $fields = ['key' => 'old_value', 'value' => 'new_value']) {
		$sql = "SELECT `{$fields['key']}`, `{$fields['value']}` FROM `" . DB_PREFIX . "relations`"
			. " WHERE `key` = '" . $key . "' AND `data_source` = '" . $source . "'";
		$rows = $this->db->query($sql)->rows;

		$result = [];
		foreach ($rows as $row) {
			$result[$row[$fields['key']]] = $row[$fields['value']];
		}

		return $result;
	}

	public function setRelationKey( $key, $old_value, $new_value, $source, $lastmode = '') {
		$sql = "INSERT `" . DB_PREFIX. "relations` (`key`, `old_value`, `new_value`, `data_source`, `lastmode`) VALUE ('" . $key ."', '". $old_value ."', '". $new_value ."', '" . $source . "', " . (($lastmode == '')? 'NOW()' : "'" . $lastmode . "'" ) . ");";
		$result = $this->db->query($sql);
		return $result;
	}
}