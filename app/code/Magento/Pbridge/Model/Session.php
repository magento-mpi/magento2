<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * @param Magento_Core_Model_Session_Validator $validator
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
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
     * @param Magento_Core_Model_Url $url
     * @param array $data
     * @param null $sessionName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Session_Validator $validator,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
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
        Magento_Core_Model_Url $url,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct(
            $validator, $logger, $eventManager, $coreHttp, $coreStoreConfig, $coreConfig, $messageFactory,
            $message, $cookie, $request, $appState, $storeManager, $dir, $url, $data
        );
        $this->init('magento_pbridge', $sessionName);
    }
}
