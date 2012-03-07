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
 * @property string name The human-readable name of this field (e.g. "Sponsor Info").
 * @property string id The ID for the field.
 * @property array values The values for this field.
 * @property array valueIds A list of the value IDs for this field.
 * @property array schema TODO:
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.fields
 */
class BasicField extends BaseField {
	
	private $_value;
	private $_name;
	private $_id;
	
	public function __constructor($id, $name) {
		$this->_id = $id;
		$this->_name = $name;
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
