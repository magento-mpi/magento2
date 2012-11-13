<?php
/**
 * Factory of web API requests.
 *
 * @copyright {}
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
     * @return Mage_Webapi_Controller_Request
     * @throws InvalidArgumentException If API type is not defined in Mage_Webapi_Controller_Front
     */
    public function get($apiType)
    {
        switch ($apiType) {
            case Mage_Webapi_Controller_Front::API_TYPE_REST:
                return $this->_objectManager->get(
                    'Mage_Webapi_Controller_Request_Rest',
                    array('apiType' => $apiType)
                );
                break;
            case Mage_Webapi_Controller_Front::API_TYPE_SOAP:
                return $this->_objectManager->get(
                    'Mage_Webapi_Controller_Request',
                    array('apiType' => $apiType)
                );
                break;
            default:
                throw new InvalidArgumentException('The "%s" API type is not valid.', $apiType);
                break;
        }
    }
}
