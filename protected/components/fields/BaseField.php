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
	 * It must only be lowercase letters, numbers and underscores.
	 * It must start with a letter.
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
	 */
	public function getValues() {
		return CMap::mergeArray(
			$this->getDataValues(),
			$this->getColumnValues()
		);
	}
	
	/**
	 * Returns an array containing values that will be stored in table columns.
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
	abstract public function getDataValues() {
		return array();
	}
	
	/**
	 * Set the values for this field.
	 * @param array $coreValues The core column values for the table record.
	 * @param array $values The values for this field, collected from columns and the 'data' column.
	 */
	abstract public function setValues(array $coreValues, array $values);
	
	/**
	 * Renders the HTML for the 'view only' representation of this field.
	 * @param CController $controller
	 */
	abstract public function renderViewOnly($controller);
	
	/**
	 * Renders the form HTML for editing this field.
	 * Use Yii::app()->clientScript->registerXX to include CSS and JavaScript.
	 * @param CController $controller
	 */
	abstract public function renderEditor($controller);
	
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
	
	public function getDependancies() {
		return array();
		
		// Example
		return array('LocationField', 'ContactField');
	}
}
