<?php
class FieldTableRenderer extends CComponent {
	
	/**
	 * @var string|null Value for the table tag's ID attribute.
	 */
	public $id = null;
	
	private $_controller;
	private $_coreValues;
	private $_values;
	private $_fields;
	
	/**
	 * Create a 
	 * @param CController $controller
	 * @param array $coreValues The core values (e.g. calendarid, description) from the table columns.
	 * @param array $values The values for the fields.
	 * @param array $fields The fields to render.
	 */
	public function __construct($controller, array $coreValues, array $fieldValues, array $fields) {
		$this->_controller = $controller;
		$this->_coreValues = $coreValues;
		$this->_values = $fieldValues;
		$this->_fields = $fields;
	}
	
	/**
	 * Render the 'view only' HTML for the fields.
	 * @param boolean $showEmpty Show fields names for fields that do not return HTML. Defaults to false.
	 */
	public function renderViewOnly($showEmpty=false) {
		$fieldsToDisplay = array();
		foreach ($this->_fields as $field) {
			if (isset($this->_values[$field->id]))
				$field->setValues($this->_coreValues, $this->_values[$field->id]);
				
			$html = $field->renderViewOnly($this->_controller, false);
			if ($html !== NULL || $showEmpty) {
				array_push($fieldsToDisplay, array('name'=>$field->name, 'html'=>$html === NULL ? '' : $html));
			}
		}
		
		if (count($fieldsToDisplay) > 0) {
			?><table <?php if (!empty($this->id)) echo 'id="' . $this->id . '"' ?> class="<?php echo Yii::app()->cssPrefix; ?>fieldtable" border="0"><tbody><?php
			foreach ($fieldsToDisplay as $field) {
				?><tr>
					<th class="<?php echo Yii::app()->cssPrefix; ?>fieldtable-label"><?php echo CHtml::encode($field['name']); ?></th>
					<td class="<?php echo Yii::app()->cssPrefix; ?>fieldtable-value"><?php echo $field['html']; ?></td>
				</tr><?php
			}
			?></tbody></table><?php
		}
	}
}