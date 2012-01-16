<?php

abstract class Mage_Api2_Model_Request_Interpreter
{
    /**
     * Request body interpreters factory
     *
     * @static
     * @throw
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

            $interpreterType = $helper->getInterpreterType($request);
        } else {
            throw new Mage_Api2_Exception(
                sprintf('Invalid Interpreter factory argument "%s"', $input),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }

        //TODO Do we need check here? Or we assume this method receives valid Interpreter definition always
        return Mage::getModel($interpreterType);
    }
}
