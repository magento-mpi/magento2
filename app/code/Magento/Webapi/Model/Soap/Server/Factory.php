<?php
/**
 * Factory to create new SoapServer objects.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap\Server;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Controller\Soap\Handler
     */
    protected $_soapHandler;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Webapi\Controller\Soap\Handler $soapHandler
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Webapi\Controller\Soap\Handler $soapHandler
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
