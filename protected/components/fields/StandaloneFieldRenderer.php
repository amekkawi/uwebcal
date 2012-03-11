<?php
/**
 * StandaloneFieldRenderer class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * A helper class that displays a single fields with the field name as a header.
 * 
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.fields
 */
class StandaloneFieldRenderer extends FieldTableRenderer {
	
	/**
	 * @var string The view that will be used by {@link StandaloneFieldRenderer::renderReadOnly}.
	 */
	public $readOnlyView = '/fields/standalonefield';
	
	/**
	 * Creates a StandaloneFieldRenderer
	 * @param CController $controller
	 * @param array $coreValues The core values (e.g. calendarid, description) from the table columns.
	 * @param array $fieldValues The values for all the fields, as returned by {@link FieldModel::ExtractData}.
	 * @param array $field The field to render.
	 */
	public function __construct(CController $controller, $field, array $fieldValues, array $coreValues=array()) {
		parent::__construct($controller, array($field), $fieldValues, $coreValues);
	}
}