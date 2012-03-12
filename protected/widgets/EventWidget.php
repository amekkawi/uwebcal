<?php
class EventWidget extends CWidget {
	
	public $event = NULL;
	public $mode = self::MODE_READONLY;
	
	public $readOnlyView = 'event/readonly';
	public $editorView = 'event/editor';
	
	const MODE_READONLY = 0;
	const MODE_EDITOR = 1;
	
	public function init() {
		if (!($this->event instanceof Event))
			throw new CHttpException(500, Yii::t('app', '{class}\'s {property} property must be an instance of {propclass}.', array('{class}'=>__CLASS__, '{property}'=>'event', '{propclass}'=>'Event')));
	}
	
	public function run() {
		
		// Set values for all the fields.
		$this->event->extractFieldData($fieldValues, $coreValues);
		foreach (Yii::app()->eventFields as $field)
			$field->unsetAttributes()->setAttributes($fieldValues, $coreValues);
		
		$this->render(
			$this->mode == self::MODE_READONLY ? $this->readOnlyView : $this->editorView,
			array('event'=>$this->event, 'fieldValues'=>$fieldValues, 'coreValues'=>$coreValues)
		);
	}
}