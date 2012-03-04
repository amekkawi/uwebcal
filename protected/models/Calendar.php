<?php
/**
 * Calendar table class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * Represents the calendar table in the database.
 * 
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.db.ar
 */
class Calendar extends CActiveRecord {
	
	const HTMLMODE_PARTS = 0;
	const HTMLMODE_TEMPLATE = 1;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CActiveRecord active record model instance.
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * Returns the name of the associated database table.
	 * @return string the table name
	 */
	public function tableName() {
		return '{{calendars}}';
	}
	
	/**
	 * Returns the primary key of the associated database table.
	 * @return string the primary key of the associated database table.
	 */
	public function primaryKey() {
		return 'calendarid';
	}
}