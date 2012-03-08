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
	
	public function setValues(array $coreValues, array $values) {
		$this->_value = isset($values['value']) && !empty($values['value']) ? $values['value'] : NULL;
	}
	
	public function renderReadOnly($controller, $echo=true) {
		if (empty($this->_value)) {
			return $echo ? false : NULL;
		}
		
		$html = CHtml::encode($this->_value);
		
		if ($echo) echo $html;
		else return $html;
		
		return true;
	}
	
	public function renderEditor($controller) {
		?><input type="text" name="<?php echo CHtml::encode($this->getValueId('value')); ?>" value="<?php echo CHtml::encode(empty($this->_value) ? '' : $this->_value); ?>" /><?php
	}
	
	public function getValueNames() {
		return array('value');
	}
}
