<?php
// v0.1.4
namespace JD_Tools;

class Relation {
	private $remote_data_source;
	private $model;
	protected $registry;
	public function __construct ($registry, $source = '')
	{
		$this->registry = $registry;

		if(empty($source)) {
			$config_key = $this->config->get('jd_sync_module');
			$this->remote_data_source = $this->config->get($config_key . '_config')['source']['from'];
		}
		else {
			$this->remote_data_source = $source;
		}



		$model = 'tool/jd_tools/relations';
		$this->load->model($model);
		$this->model = 'model_' . str_replace(array('/', '-', '.'), array('_', '', ''), $model);
	}

	public function __get($name) {
		return $this->registry->get($name);
	}

	/**
	 * Отримує новий ід, якщо нода вже була імпортована в нову БД
	 *
	 * По-суті це перевірка на існування Запису в БД
	 * @param $key
	 * @param $value
	 *
	 * @return mixed
	 */
	public function getNewKeyValue( $key, $value) {
		return $this->{$this->model}->getRelationKey($key, $value, $this->remote_data_source);
	}
	public function getLastMode($key, $value) {
		return $this->{$this->model}->getLastMode($key, $value, $this->remote_data_source);
	}

	public function getOldKeyValue( $key, $value ) {
		return $this->{$this->model}->getOldRelationKey($key, $value, $this->remote_data_source);
	}

	public function setRelationKey($key, $old_value, $new_value, $lastmod = '') {
		return $this->{$this->model}->setRelationKey($key, $old_value, $new_value, $this->remote_data_source, $lastmod);
	}

	public function updateRelationValue($key, $source_value, $new_value) {
		return $this->{$this->model}->updateRelationValue($key, $source_value, $new_value, $this->remote_data_source);
	}

	public function checkForUpdateNeeds($table, $id) {
		$db_prefix = DB_PREFIX;
		$id = (int) explode('-', $id)[0];
		$sql = "SELECT `date_modified` FROM `{$db_prefix}{$table}` WHERE `{$table}_id` = '{$id}'";

		$data = $this->db->query($sql);
		if($data->num_rows > 0) {
			$modified = strtotime($data->row['date_modified']);
			$today = strtotime('today');

			if($modified > $today) {
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}
}