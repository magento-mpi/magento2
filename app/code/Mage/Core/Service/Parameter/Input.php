<?php
class Mage_Core_Service_Parameter_Input extends Varien_Object
{
    /**
     * @param string $serviceMethodId ID of the method to which this object is being injected
     * @param array  $data            Parameters which are going to be passed to the method
     * @throws Mage_Core_Service_Parameter_Exception
     */
    public function __construct($serviceMethodId, array $data = array())
    {
        parent::__construct($data);

        if (!$this->_conformsWithSchema($serviceMethodId)) {
            throw new Mage_Core_Service_Parameter_Exception(sprintf(
                'Passed parameters do not conform with %s method XSD schema.', $serviceMethodId
            ));
        }
    }

    /**
     * Checks whether submitted parameters conforms with method's schema which this object is injected to.
     *
     * @param string $serviceMethodId
     * @return bool
     */
    protected function _conformsWithSchema($serviceMethodId)
    {
        // @todo
        return true;
    }
}
