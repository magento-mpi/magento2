<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Server_Factory implements Magento_Webapi_Model_Soap_Server_FactoryInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create SoapServer
     *
     * @param string $url URL of a WSDL file
     * @param array $options Options including encoding, soap_version etc
     * @param object $handler Handler object to handle soap requests
     * @return SoapServer
     */
    public function create($url, $options, $handler)
    {
        $soapServer = $this->_objectManager->create(
            'SoapServer',
            array(
                'wsdl' => $url,
                'options' => $options
            )
        );
        $soapServer->setObject($handler);
        return $soapServer;
    }
}