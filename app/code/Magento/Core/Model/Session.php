<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core session model
 *
 * @todo extend from Magento_Core_Model_Session_Abstract
 *
 * @method null|bool getCookieShouldBeReceived()
 * @method Magento_Core_Model_Session setCookieShouldBeReceived(bool $flag)
 * @method Magento_Core_Model_Session unsCookieShouldBeReceived()
 */
class Magento_Core_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Model_Session_Validator $validator
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Message_CollectionFactory $messageFactory
     * @param Magento_Core_Model_Message $message
     * @param Magento_Core_Model_Cookie $cookie
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Url_Proxy $url
     * @param array $data
     * @param string $sessionName
     */
    public function __construct(
        Magento_Core_Model_Session_Validator $validator,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Message_CollectionFactory $messageFactory,
        Magento_Core_Model_Message $message,
        Magento_Core_Model_Cookie $cookie,
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Url_Proxy $url,
        array $data = array(),
        $sessionName = null
    ) {
        $this->_coreData = $coreData;
        parent::__construct($validator, $logger, $eventManager, $coreHttp, $coreStoreConfig, $coreConfig,
            $messageFactory, $message, $cookie, $request, $appState, $storeManager, $dir, $url, $data);
        $this->init('core', $sessionName);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->getData('_form_key')) {
            $this->setData('_form_key', $this->_coreData->getRandomString(16));
        }
        return $this->getData('_form_key');
    }
}
