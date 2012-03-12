<?php
/**
 * JSONValidator class file.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

/**
 * JSONValidator validates that the attribute value can be converted to JSON.
 *
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @package app.validators
 */
class JSONValidator extends CValidator
{
        /**
         * @var boolean whether the attribute value can be null or empty. Defaults to true,
         * meaning that if the attribute is empty, it is considered valid.
         */
        public $allowEmpty = true;
        
        public $enableClientValidation = false;

        /**
         * Validates the attribute of the object.
         * If there is any error, the error message is added to the object.
         * @param CModel $object the object being validated
         * @param string $attribute the attribute being validated
         */
        protected function validateAttribute($object, $attribute) {
                $value = $object->$attribute;
                if($this->allowEmpty && $this->isEmpty($value))
                        return;
                if(CJSON::encode($value) === '') {
                        $message = $this->message !== null ? $this->message : Yii::t('app','{attribute} must be able to be converted to JSON.');
                        $this->addError($object, $attribute, $message);
                }
        }
}