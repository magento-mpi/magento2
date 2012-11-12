<?php
/**
 * API request factory.
 *
 * @copyright {copyright}
 */
class Mage_Webapi_Controller_RequestFactory
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
     * Create request object.
     *
     * @param string $apiType
     * @return Mage_Webapi_Controller_RequestAbstract
     * @throws InvalidArgumentException If API type is not defined in Mage_Webapi_Controller_Front_Base
     */
    public function getRequest($apiType)
    {
        switch($apiType) {
            case Mage_Webapi_Controller_Front_Base::API_TYPE_REST:
                return $this->_objectManager->get('Mage_Webapi_Controller_Request_Rest');
                break;
            case Mage_Webapi_Controller_Front_Base::API_TYPE_SOAP:
                return $this->_objectManager->get('Mage_Webapi_Controller_Request_Soap');
                break;
            default:
                throw new InvalidArgumentException('The "%s" API type is not valid.', $apiType);
                break;
        }
    }
}
