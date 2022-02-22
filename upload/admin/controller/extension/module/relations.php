<?php
class ControllerExtensionModuleRelations extends Controller {
	private $code = 'module_relations';
	private $setting = [
		"module_relations_status"   =>  1
	];


	public function install() {
		$this->load->model('tool/jd_tools/relations');
		$this->model_tool_jd_tools_relations->createRelationsTable();

		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting($this->code, $this->setting);
	}
	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting($this->code);
	}
}