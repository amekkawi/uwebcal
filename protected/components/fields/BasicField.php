<?php
/**
 * BasicField class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * A basic field with one value.
 * 
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.fields
 */
class BasicField extends BaseField {
	
	private $_value = NULL;
	private $_name;
	private $_id;
	
	public function setName($name) {
		if (isset($this->_name)) throw new CHttpException(500, Yii::t('app', '{class}\'s "{prop}" property cannot be changed after it has been set.', array('{class}'=>__CLASS__, '{prop}'=>'name')));
		$this->_name = $name;
	}
	
	public function setId($id) {
		if (isset($this->_id)) throw new CHttpException(500, Yii::t('app', '{class}\'s "{prop}" property cannot be changed after it has been set.', array('{class}'=>__CLASS__, '{prop}'=>'id')));
		$this->_id = $id;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function getId() {
		return $this->_id;
	}
	
	public function getDataValues() {
		return array(
			'value'=>$this->_value
		);
	}
	
	public function setValues(array $values, array $coreValues = array()) {
		if (!empty($values['value'])) $this->_value = $values['value'];
		return $this;
	}
	
	public function resetValues() {
		$this->_value = NULL;
		return $this;
	}
	
	public function renderReadOnly($controller, $return=false) {
		if (empty($this->_value)) {
			return $return ? NULL : false;
		}
		
		$html = $controller->renderPartial('/fields/basic/readonly', array('field'=>$this, 'value'=>$this->_value), true);
		
		if ($return) return $html;
		else echo $html;
		
		return true;
	}
	
	public function renderEditor($controller, $return=false) {
		return $controller->renderPartial('/fields/basic/editor', array('field'=>$this, 'value'=>$this->_value), $return);
	}
	
	public function getValueNames() {
		return array('value');
	}
}
