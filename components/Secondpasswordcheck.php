<?php

use yii\validators\Validator;
use Yii;

class Secondpasswordcheck extends Validator
{
    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($object,$attribute)
    {
        $pattern = $this->pattern;

        // extract the attribute value from it's model object
        $value = $object->$attribute;

        if(!preg_match($pattern, $value))
        {
            $this->addError($object, $attribute, $this->err_msg);
        }
    }

    /**
     * Implementing Client Validation
     *
     * Returns the JavaScript needed for performing client-side validation.
     * @param CModel $object the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     * @return string the client-side validation script.
     * @see CActiveForm::enableClientValidation
     */
    public function clientValidateAttribute($object,$attribute)
    {

        // check the strength parameter used in the validation rule of our model
        $pattern = $this->pattern;

        //replace {attribute} with correct label
        $params['{attribute}']=$object->getAttributeLabel($attribute);
        $error_message = strtr($this->err_msg,$params);

        return "
        if(value.match(".$pattern.")) {
            messages.push(".CJSON::encode($error_message).");
        }
        ";
    }
}