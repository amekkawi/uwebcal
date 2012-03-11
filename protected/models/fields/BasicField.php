<?php
class BasicField extends FieldModel {
	private $_label;
	private $_name;
	
	public $value = NULL;
	public $customRules = array();
	public $readOnlyView = '/fields/basic/readonly';
	public $editorView = '/fields/basic/editor';
	
	public function setLabel($label) {
		if (isset($this->_label)) throw new CHttpException(500, Yii::t('app', '{class}\'s "{prop}" property cannot be changed after it has been set.', array('{class}'=>__CLASS__, '{prop}'=>'label')));
		$this->_label = $label;
	}
	
	public function setName($name) {
		if (isset($this->_name)) throw new CHttpException(500, Yii::t('app', '{class}\'s "{prop}" property cannot be changed after it has been set.', array('{class}'=>__CLASS__, '{prop}'=>'name')));
		$this->_name = $name;
	}
	
	public function getLabel() {
		return $this->_label;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function attributeNames() {
		return array('value');
	}
	
	public function rules() {
		return array_merge(
			$this->customRules,
			array(
				array('value', 'safe', 'skipOnError'=>true)
			)
		);
	}
	
	public function renderReadOnly(CController $controller, $return=false) {
		if (empty($this->value)) {
			return $return ? NULL : false;
		}
		
		$html = $controller->renderPartial($this->readOnlyView, array('field'=>$this, 'value'=>$this->value), true);
		
		if ($return) return $html;
		else echo $html;
		
		return true;
	}
	
	public function renderEditor(CController $controller, $return=false) {
		return $controller->renderPartial($this->editorView, array('field'=>$this, 'value'=>$this->value), $return);
	}
}