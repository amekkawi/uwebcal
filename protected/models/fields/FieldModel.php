<?php
/**
 * 
 * Enter description here ...
 * @property string $label The human-readable label for this field (e.g. "Sponsor Info").
 * @property string $name A name that uniquely identifies this field.
 * @property array $dataAttributes TODO: (name=>value).
 * @property array $columnAttributes TODO: (name=>value).
 * 
 * @author amekkawi
 *
 */
abstract class FieldModel extends CModel {
	
	private $_coreValues = NULL;
	
	/**
	 * Returns the human-readable label for this field (e.g. "Sponsor Info").
	 * @return string The field name. 
	 */
	abstract public function getLabel();
	
	/**
	 * Returns a name that uniquely identifies this field.
	 * The name must only be lowercase letters and numbers, and must start with a letter.
	 * @return string The field's ID.
	 */
	abstract public function getName();
	
	/**
	 * Get a list of the attribute names for this field.
	 * Attribute names must only be lowercase letters and numbers, and must start with a letter.
	 * @return array The attribute names.
	 */
	//abstract public function attributeNames();
	
	/**
	 * Get a list of the attribute names for this field that are stored in table columns.
	 * Attribute names must only be lowercase letters and numbers, and must start with a letter.
	 * @return array The attribute names.
	 */
	public function columnAttributeNames() {
		return array();
	}
	
	/**
	 * Get a list of the attribute names for this field that are stored in the table's data column.
	 * Attribute names must only be lowercase letters and numbers, and must start with a letter.
	 * @return array The attribute names.
	 */
	public function dataAttributeNames() {
		return array();
	}
	
	final public function getFullAttributeName($name) {
		return $this->getName() . '_' . $name;
	}
	
	final public function getFullAttributeNames(array $names=NULL) {
		$fullNames = array();
		foreach (($names === NULL ? $this->attributeNames() : $names) as $name) {
			$fullNames[] = $this->getFullAttributeName($name);
		}
		return $fullNames;
	}
	
	public function getColumnAttributes() {
		return array();
	}
	
	public function getDataAttributes() {
		return array();
	}
	
	public function unsetAttributes() {
		$this->_coreValues = NULL;
		parent::unsetAttributes();
		return $this;
	}
	
	public function setAttributes($values, $coreValues=array(), $safeOnly=true) {
		
		if (is_bool($coreValues)) {
			$safeOnly = $coreValues;
			$coreValues = array();
		}
		
		$this->_coreValues = $coreValues;
		parent::setAttributes($values);
		return $this;
	}
	
	/**
	 * Renders the HTML for the 'read only' representation of this field.
	 * @param CController $controller
	 * @param boolean $return whether the rendering result should be returned instead of being displayed to end users.
	 * 
	 * @return boolean|string|null If $return is false then this will return a boolean
	 *                             declaring whether or not the field could be rendered.
	 *                             
	 *                             If $return is true then this will return a string if the
	 *                             field could be rendered, otherwise it will return null.
	 */
	abstract public function renderReadOnly(CController $controller, $return=false);
	
	/**
	 * Renders the form HTML for editing this field.
	 * Use Yii::app()->clientScript->registerXX to include CSS and JavaScript.
	 * @param CController $controller
	 * @param boolean $return whether the rendering result should be returned instead of being displayed to end users.
	 * @return the rendering result. Null if the rendering result is not required. (see {@link CController::renderPartial})
	 */
	abstract public function renderEditor(CController $controller, $return=false);
	
	/**
	 * Extract the core and field values from an array of column values.
	 * @param array $columns The table column values. See {@link CActiveRecord::attributes}.
	 * @param array $coreValues Variable to which an array of core values will be set. You may pass an existing array to set default values,
	 * @param array $fieldValues Variable to which an array of field values will be set. You may pass an existing array to set default values,
	 */
	final static public function ExtractData($columns, &$fieldValues, &$coreValues=NULL) {
		if (!is_array($coreValues)) $coreValues = array();
		if (!is_array($fieldValues)) $fieldValues = array();
		
		// Parse JSON data
		if (!empty($columns['data']) && ($data = CJSON::decode($columns['data'])) !== NULL) {
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					$fieldValues[$key] = $value;
				}
				else {
					$coreValues[$key] = $value;
				}
			}
		}
		
		// Extract the core and field values.
		foreach ($columns as $colName => $colValue) {
			if (strpos($colName, 'f_') === 0 && count($split = explode('_', $colName)) == 3) {
				if (!isset($fieldValues[$split[1]])) $fieldValues[$split[1]] = array();
				$fieldValues[$split[1]][$split[2]] = $colValue;
			}
			else {
				$coreValues[$colName] = $colValue;
			}
		}
	}
}