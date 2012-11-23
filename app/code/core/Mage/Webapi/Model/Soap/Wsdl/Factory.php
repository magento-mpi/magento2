<?php
/**
 * Factory of WSDL builders.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_Wsdl_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create WSDL builder instance.
     *
     * @param string $wsdlName
     * @param string $endpointUrl
     * @return Mage_Webapi_Model_Soap_Wsdl
     */
    public function create($wsdlName, $endpointUrl)
    {
        return $this->_objectManager->create(
            'Mage_Webapi_Model_Soap_Wsdl',
            array(
                'name' => $wsdlName,
                'uri' => $endpointUrl,
            ),
            false
        );
    }
}
