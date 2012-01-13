<?php

abstract class Mage_Api2_Model_Request_Interpreter
{
    /**
     * Request body interpreters factory
     *
     * @static
     * @param mixed $input
     * @return Mage_Api2_Model_Request_Interpreter_Interface
     */
    public static function factory($input=null)
    {
        if (is_string($input)) {
            $interpreterType = $input;
        } elseif ($input instanceof Mage_Api2_Model_Request) {
            $request = $input;

            /** @var $helper Mage_Api2_Helper_Data */
            $helper = Mage::helper('api2');

            $interpreterType = $helper->getInterpreterType($request);    //this can also throw Exception with code 406 for example
        } else {
            throw new Exception('');
        }



        return Mage::getModel($interpreterType);
    }
}
