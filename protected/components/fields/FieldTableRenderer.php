<?php
/**
 * FieldTableRenderer class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * A helper class that displays multiple fields in a two column table.
 * The left column has the field name and the right column has the field HTML.
 * 
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.fields
 */
class FieldTableRenderer extends CComponent {
	
	/**
	 * @var string|null Value for the table tag's ID attribute.
	 */
	public $id = null;
	
	/**
	 * @var string The view that will be used by {@link FieldTableRenderer::renderReadOnly}.
	 */
	public $readOnlyView = '/fields/fieldtable';
	
	protected $_controller;
	protected $_coreValues;
	protected $_values;
	protected $_fields;
	
	/**
	 * Creates a FieldTableRenderer.
	 * @param CController $controller
	 * @param array $coreValues The core values (e.g. calendarid, description) from the table columns.
	 * @param array $fieldValues The values for all the fields, as returned by {@link WebApplication::getFieldValuesFromColumns}.
	 * @param array $fields The fields to render.
	 */
	public function __construct($controller, array $coreValues, array $fieldValues, array $fields) {
		$this->_controller = $controller;
		$this->_coreValues = $coreValues;
		$this->_values = $fieldValues;
		$this->_fields = $fields;
	}
	
	/**
	 * Render the 'read only' HTML for the fields.
	 * @param boolean $showEmpty Show fields names for fields that do not return HTML. Defaults to false.
	 */
	public function renderReadOnly($showEmpty=false) {
		$fieldsToDisplay = array();
		foreach ($this->_fields as $field) {
			$field->setValues($this->_coreValues, isset($this->_values[$field->id]) ? $this->_values[$field->id] : array());
				
			$html = $field->renderReadOnly($this->_controller, true);
			if ($html !== NULL || $showEmpty) {
				array_push($fieldsToDisplay, array('name'=>$field->name, 'html'=>$html === NULL ? '' : $html));
			}
		}
		
		if (count($fieldsToDisplay) > 0 || $showEmpty) {
			$this->_controller->renderPartial($this->readOnlyView, array('id'=>$this->id, 'fields'=>$fieldsToDisplay));
		}
	}
}