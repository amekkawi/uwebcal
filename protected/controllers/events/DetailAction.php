<?php
class DetailAction extends CAction {
	function run($calendarid, $id) {
		
		$eventId = $id;
		$repeatIndex = 0;
		
		// Parse out the repeatIndex from the id, if it exists.
		if (($pos = strrpos($id, '-')) !== FALSE) {
			$eventId = substr($id, 0, $pos);
			$repeatIndex = (int)substr($id, $pos + 1);
		}
		
		// Get the event record.
		if (($event = Event::model()->findByPk(array('calendarid'=>$calendarid, 'eventid'=>$eventId, 'repeatindex'=>$repeatIndex))) === NULL) {
			throw new CHttpException(404, Yii::t('app', 'An event with an ID of "{id}" does not exist.', array('{id}' => $id)));
		}
		
		// Extract out the core and field values from the event columns.
		$event->extractFieldData($fieldValues, $coreValues);
		
		$this->controller->render('detail', array('event'=>$event, 'coreValues'=>$coreValues, 'fieldValues'=>$fieldValues));
	}
}