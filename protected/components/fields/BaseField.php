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
 * @property array valueIds TODO:
 * @property array schema TODO:
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.fields
 */
abstract class BaseField extends CComponent {
	
	private $_value;
	private $_owner;
	
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
	 * @return array The values to be stored.
	 */
	public function getColumnValues() {
		return array();
		
		// Example
		return array(
			'fieldA'=>'value',
			'fieldB'=>'value'
		);
	}
	
	/**
	 * Returns an array containing values that will be stored in the 'data' column as JSON.
	 * Store values in the 'data' column that will only be displayed and do not need to be searchable.
	 * @return array The values to be stored.
	 */
	public function getDataValues() {
		return array(
			'value'=>$_value
		);
		
		// Example
		return array(
			'fieldA'=>'value',
			'fieldB'=>'value'
		);
	}
	
	/**
	 * Set the values for this field.
	 * @param array $coreValues The core column values for the table record.
	 * @param array $values The values for this field, collected from columns and the 'data' column.
	 */
	public function setValues(array $coreValues, array $values) {
		$this->_value = $values['value'];
	}
	
	/**
	 * Renders the 'view only' representation of this field.
	 * @param CController $controller
	 */
	public function renderViewOnly($controller) {
		echo CHtml::encode($this->_value);
	}
	
	/**
	 * Renders the form HTML for editing this field.
	 * @param CController $controller
	 * @param array $valueIdMap
	 */
	public function renderEditor($controller, array $valueIdMap) {
		?><input type="text" name="<?php echo CHtml::encode($valueIdMap['value']); ?>" value="<?php echo CHtml::encode($this->_value); ?>" /><?php
	}
	
	public function getTableSchemas() {
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
	
	public function getValueNames() {
		return array('value');
	}
	
	final public function getValueId($valueName) {
		return $this->getId() . '_' . $valueName;
	}
	
	final public function getValueIds() {
		$ids = array();
		foreach ($this->getValueNames() as $name) {
			array_push($ids, $this->getValueId($name));
		}
		return $ids;
	}
	
	public function getSchema() {
		return array();
		
		// Example
		return array(
			array('column', 'COLUMN_NAME', 'TYPE'),
			array('index', 'INDEX_NAME', 'COLUMN_NAME', true /* unique */ ),
			array('index', 'INDEX_NAME', 'COLUMN_NAME,COLUMN_NAME', true /* unique */ )
		);
	}
}
