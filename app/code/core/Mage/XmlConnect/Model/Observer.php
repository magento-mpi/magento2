<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect module observer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Observer
{
    /**
     * List of config field names which changing affects mobile applications behaviour
     *
     * @var array
     */
    protected $_appDependOnConfigFieldPathes = array(
        Mage_XmlConnect_Model_Application::XML_PATH_PAYPAL_BUSINESS_ACCOUNT,
        'sendfriend/email/max_recipients',
        'sendfriend/email/allow_guest',
        'general/locale/code',
        'currency/options/default',
        Mage_XmlConnect_Model_Application::XML_PATH_SECURE_BASE_LINK_URL,
        Mage_XmlConnect_Model_Application::XML_PATH_GENERAL_RESTRICTION_IS_ACTIVE,
        Mage_XmlConnect_Model_Application::XML_PATH_GENERAL_RESTRICTION_MODE,
        Mage_XmlConnect_Model_Application::XML_PATH_DEFAULT_CACHE_LIFETIME
    );

    /**
     * Stop website stub or private sales restriction
     *
     * @param Varien_Event_Observer $observer
     */
    public function restrictWebsite($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'xmlconnect') {
            $observer->getEvent()->getResult()->setShouldProceed(false);
        }
    }

    /**
     * Update all applications "updated at" parameter with current date on save some configurations
     *
     * @param Varien_Event_Observer $observer
     */
    public function changeUpdatedAtParamOnConfigSave($observer)
    {
        $configData = $observer->getEvent()->getConfigData();
        if ($configData && (int)$configData->isValueChanged()
            && in_array($configData->getPath(), $this->_appDependOnConfigFieldPathes)
        ) {
            Mage::getModel('Mage_XmlConnect_Model_Application')->updateAllAppsUpdatedAtParameter();
        }
    }

    /**
     * Send a message if Start Date (Queue Date) is empty
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function sendMessageImmediately($observer)
    {
        $message = $observer->getEvent()->getData('queueMessage');
        if ($message instanceof Mage_XmlConnect_Model_Queue && (strtolower($message->getExecTime()) == 'null'
            || !$message->getExecTime())
        ) {
            $message->setExecTime(Mage::getSingleton('Mage_Core_Model_Date')->gmtDate());
            Mage::helper('Mage_XmlConnect_Helper_Data')->sendBroadcastMessage($message);
            return true;
        }

        return false;
    }

    /**
     * Send scheduled messages
     *
     * @return null
     */
    public function scheduledSend()
    {
        $countOfQueue = Mage::getStoreConfig(Mage_XmlConnect_Model_Queue::XML_PATH_CRON_MESSAGES_COUNT);

        $collection = Mage::getModel('Mage_XmlConnect_Model_Queue')->getCollection()->addOnlyForSendingFilter()
            ->setPageSize($countOfQueue)->setCurPage(1)->load();

        foreach ($collection as $message) {
            if ($message->getId()) {
                Mage::helper('Mage_XmlConnect_Helper_Data')->sendBroadcastMessage($message);
                $message->save();
            }
        }
    }
}
