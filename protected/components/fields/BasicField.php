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
	
	private $_value;
	private $_name;
	private $_id;
	
	public function setName($name) {
		$this->_name = $name;
	}
	
	public function setId($id) {
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
			'value'=>$_value
		);
	}
	
	public function setValues(array $coreValues, array $values) {
		$this->_value = $values['value'];
	}
	
	public function renderViewOnly($controller) {
		echo CHtml::encode($this->_value);
	}
	
	public function renderEditor($controller) {
		?><input type="text" name="<?php echo CHtml::encode($this->getValueId('value')); ?>" value="<?php echo CHtml::encode($this->_value); ?>" /><?php
	}
	
	public function getValueNames() {
		return array('value');
	}
}
