<?php
/**
 * BaseField class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * The base class for custom calendar and event fields.
 * 
 * @property string name The human-readable name of this field (e.g. "Sponsor Info").
 * @property string id The ID for the field.
 * @property array values The values for this field.
 * @property array valueIds A list of the value IDs for this field.
 * @property array schema An array of arrays that contain column and index schema.
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.fields
 */
abstract class BaseField extends CComponent {
	
	/**
	 * Returns the human-readable name of this field (e.g. "Sponsor Info").
	 * @return string The field name. 
	 */
	abstract public function getName();
	
	/**
	 * Returns an ID for the field.
	 * Ids must only be lowercase letters and numbers, and must start with a letter.
	 * @return string The field's ID.
	 */
	abstract public function getId();
	
	/**
	 * Get the full ID for a value.
	 * @param string $valueName
	 * @return string The ID for the value.
	 */
	final public function getValueId($valueName) {
		return $this->getId() . '_' . $valueName;
	}
	
	/**
	 * Get all the full value IDs for this field.
	 * @return array The list of value IDs.
	 */
	final public function getValueIds() {
		$ids = array();
		foreach ($this->getValueNames() as $name) {
			array_push($ids, $this->getValueId($name));
		}
		return $ids;
	}
	
	/**
	 * Get the values for this field.
	 * Value names must only be lowercase letters and numbers, and must start with a letter.
	 */
	public function getValues() {
		return CMap::mergeArray(
			$this->getDataValues(),
			$this->getColumnValues()
		);
	}
	
	/**
	 * Returns an array containing values that will be stored in table columns.
	 * Values must be scalar (e.g. integer, float, string or boolean).
	 * Value names must only be lowercase letters and numbers, and must start with a letter.
	 * Store values in table columns that need to be searchable.
	 * 
	 * // Example
	 * return array(
	 * 	'fieldA'=>'value',
	 * 	'fieldB'=>'value'
	 * );
	 * 
	 * @return array The values to be stored.
	 */
	public function getColumnValues() {
		return array();
	}
	
	/**
	 * Returns an array containing values that will be stored in the 'data' column as JSON.
	 * Value names must only be lowercase letters and numbers, and must start with a letter.
	 * Store values in the 'data' column that will only be displayed and do not need to be searchable.
	 * 
	 * // Example
	 * return array(
	 * 	'fieldA'=>'value',
	 * 	'fieldB'=>'value'
	 * );
	 * 
	 * @return array The values to be stored.
	 */
	public function getDataValues() {
		return array();
	}
	
	/**
	 * Set the values for this field.
	 * @param array $values The values for this field, collected from columns and the 'data' column.
	 * @param array $coreValues The core column values for the table record.
	 * @return BaseField Returns $this to allow for chaining.
	 */
	abstract public function setValues(array $values, array $coreValues = array());
	
	/**
	 * Reset all values for the field to their default value.
	 * @return BaseField Returns $this to allow for chaining.
	 */
	abstract public function resetValues();
	
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
	abstract public function renderReadOnly($controller, $return=false);
	
	/**
	 * Renders the form HTML for editing this field.
	 * Use Yii::app()->clientScript->registerXX to include CSS and JavaScript.
	 * @param CController $controller
	 * @param boolean $return whether the rendering result should be returned instead of being displayed to end users.
	 * @return the rendering result. Null if the rendering result is not required. (see {@link CController::renderPartial})
	 */
	abstract public function renderEditor($controller, $return=false);
	
	private function getTableSchemas() {
		return array();
		
		// Example
		return array(
			'tablename'=>array(
				array('column', 'COLUMN_NAME', 'TYPE'),
				array('index', 'INDEX_NAME', 'COLUMN_NAME' ),
				array('index', 'INDEX_NAME', 'COLUMN_NAME,COLUMN_NAME', 'unique'=>true ),
				array('foreignkey', 'FK_NAME', 'COLUMN_NAME,COLUMN_NAME', 'REF_TABLE_NAME', 'COLUMN_NAME,COLUMN_NAME'),
				array('foreignkey', 'FK_NAME', 'COLUMN_NAME,COLUMN_NAME', 'REF_TABLE_NAME', 'COLUMN_NAME,COLUMN_NAME', 'delete'=>'CASCADE', 'update'=>'RESTRICT'),
				array('primarykey', array('NAME','NAME')),
			)
		);
	}
	
	/**
	 * Get a list of the value names for this field.
	 * Value names must only be lowercase letters and numbers, and must start with a letter.
	 * @return array The value names.
	 */
	abstract public function getValueNames();
	
	/**
	 * Returns an array of arrays that contain column and index schema.
	 * 
	 * // Example
	 * return array(
	 * 	array('column', 'COLUMN_NAME', 'TYPE'),
	 * 	array('index', 'INDEX_NAME', 'COLUMN_NAME' ),
	 * 	array('index', 'INDEX_NAME', 'COLUMN_NAME,COLUMN_NAME', 'unique'=>true )
	 * );
	 * 
	 * @return array The schema.
	 */
	public function getSchema() {
		return array();
	}
	
	/**
	 * Get a list of other field class names that this field is dependant on.
	 * 
	 * // Example
	 * return array('LocationField', 'ContactField');
	 * 
	 * @return The list of field class names.
	 */
	public function getDependancies() {
		return array();
	}
}
