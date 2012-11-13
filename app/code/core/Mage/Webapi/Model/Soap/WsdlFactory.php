<?php
/**
 * Factory of WSDL builders.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_WsdlFactory
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
    public function createWsdl($wsdlName, $endpointUrl)
    {
        // TODO: Temporary solution because of MAGETWO-4956
        $complexTypeStrategy = $this->_objectManager->create(
            'Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased'
        );

        return $this->_objectManager->create(
            'Mage_Webapi_Model_Soap_Wsdl',
            array(
                'name' => $wsdlName,
                'uri' => $endpointUrl,
                // TODO: Temporary solution because of MAGETWO-4956
                'strategy' => $complexTypeStrategy
            ),
            false
        );
    }
}
