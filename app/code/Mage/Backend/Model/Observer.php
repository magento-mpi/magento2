<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend event observer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Observer
{
    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Model_Locale_Manager
     */
    protected $_localeManager;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Constructor
     *
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Backend_Model_Locale_Manager $localeManager
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(
        Mage_Backend_Model_Session $session,
        Mage_Backend_Model_Locale_Manager $localeManager,
        Mage_Core_Controller_Request_Http $request
    ) {
        $this->_session = $session;
        $this->_localeManager = $localeManager;
        $this->_request = $request;
    }

    /**
     * Bind backend locale
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Backend_Model_Observer
     */
    public function bindLocale($observer)
    {
        $localeModel = $observer->getEvent()->getLocale();
        if ($localeModel) {

            $forceLocale = $this->_request->getParam('locale', null);
            $sessionLocale = $this->_session->getSessionLocale();
            $userLocale = $this->_localeManager->getUserInterfaceLocale();

            $localeCodes = array_filter(array($forceLocale, $sessionLocale, $userLocale));

            if (count($localeCodes)) {
                $localeModel->setLocaleCode(reset($localeCodes));
            }
        }

        return $this;
    }

    /**
     * Prepare massaction separated data
     *
     * @return Mage_Backend_Model_Observer
     */
    public function massactionPrepareKey()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if ($key = $request->getPost('massaction_prepare_key')) {
            $postData = $request->getPost($key);
            $value = is_array($postData) ? $postData : explode(',', $postData);
            $request->setPost($key, $value ? $value : null);
        }
        return $this;
    }

    /**
     * Clear result of configuration files access level verification in system cache
     *
     * @return Mage_Backend_Model_Observer
     */
    public function clearCacheConfigurationFilesAccessLevelVerification()
    {
        Mage::app()->removeCache(Mage_Adminhtml_Block_Notification_Security::VERIFICATION_RESULT_CACHE_KEY);
        return $this;
    }
}
