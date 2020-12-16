<?php
class ModelToolJDToolsRelations extends Model {
	public function getOldRelationKey($key, $value, $source) {
		$sql = "SELECT `old_value` FROM `" . DB_PREFIX . "relations` WHERE `key` = '" . $key . "' AND `new_value` = '" . $value . "' AND `data_source` = '" . $source . "'";
		$result = $this->db->query($sql);

		return $result->row['old_value'];
	}
}