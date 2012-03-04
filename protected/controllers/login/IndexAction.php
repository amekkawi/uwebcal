<?php
class IndexAction extends CAction {
	function run() {
		$this->controller->forward('/login/standard');
	}
}