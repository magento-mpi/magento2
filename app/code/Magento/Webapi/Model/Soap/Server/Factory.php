<?php
/**
 * Creates new SoapServer objects.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Server_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Webapi_Controller_Soap_Handler
     */
    protected $_soapHandler;

    /**
     * Initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Webapi_Controller_Soap_Handler $soapHandler
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Webapi_Controller_Soap_Handler $soapHandler
    ) {
        $this->_objectManager = $objectManager;
        $this->_soapHandler = $soapHandler;
    }

    /**
     * Create SoapServer
     *
     * @param string $url URL of a WSDL file
     * @param array $options Options including encoding, soap_version etc
     * @return SoapServer
     */
    public function create($url, $options)
    {
        $soapServer = $this->_objectManager->create(
            'SoapServer',
            array(
                'wsdl' => $url,
                'options' => $options
            )
        );
        $soapServer->setObject($this->_soapHandler);
        return $soapServer;
    }
}