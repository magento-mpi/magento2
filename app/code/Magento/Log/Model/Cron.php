<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Cron Model
 *
 * @category   Magento
 * @package    Magento_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Cron extends Magento_Core_Model_Abstract
{
    const XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE     = 'system/log/error_email_template';
    const XML_PATH_EMAIL_LOG_CLEAN_IDENTITY     = 'system/log/error_email_identity';
    const XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT    = 'system/log/error_email';
    const XML_PATH_LOG_CLEAN_ENABLED            = 'system/log/enabled';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translate;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Log_Model_Log
     */
    protected $_log;

    /**
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param Magento_Core_Model_Email_TemplateFactory $templateFactory
     * @param Magento_Log_Model_Log $log
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Translate $translate
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Email_TemplateFactory $templateFactory,
        Magento_Log_Model_Log $log,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Translate $translate,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_log = $log;
        $this->_storeManager = $storeManager;
        $this->_translate = $translate;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Send Log Clean Warnings
     *
     * @return Magento_Log_Model_Cron
     */
    protected function _sendLogCleanEmail()
    {
        if (!$this->_errors) {
            return $this;
        }
        if (!$this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT)) {
            return $this;
        }


        $this->_translate->setTranslateInline(false);

        $emailTemplate = $this->_templateFactory->create();
        /* @var $emailTemplate Magento_Core_Model_Email_Template */
        $emailTemplate->setDesignConfig(
            array(
                'area' => 'backend',
                'store' => $this->_storeManager->getStore()->getId()
            )
        )->sendTransactional(
            $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE),
            $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_LOG_CLEAN_IDENTITY),
            $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT),
            null,
            array('warnings' => join("\n", $this->_errors))
        );

        $this->_translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Clean logs
     *
     * @return Magento_Log_Model_Cron
     */
    public function logClean()
    {
        if (!$this->_coreStoreConfig->getConfigFlag(self::XML_PATH_LOG_CLEAN_ENABLED)) {
            return $this;
        }

        $this->_errors = array();

        try {
            $this->_log->clean();
        } catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
        }

        $this->_sendLogCleanEmail();

        return $this;
    }
}
