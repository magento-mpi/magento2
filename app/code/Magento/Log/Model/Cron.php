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
namespace Magento\Log\Model;

class Cron extends \Magento\Core\Model\AbstractModel
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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translate;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Log\Model\Log
     */
    protected $_log;

    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Email\Model\TemplateFactory $templateFactory
     * @param \Magento\Log\Model\Log $log
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\TranslateInterface $translate
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Magento\Log\Model\Log $log,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\TranslateInterface $translate,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
     * @return \Magento\Log\Model\Cron
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
        /* @var $emailTemplate \Magento\Email\Model\Template */
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
     * @return \Magento\Log\Model\Cron
     */
    public function logClean()
    {
        if (!$this->_coreStoreConfig->getConfigFlag(self::XML_PATH_LOG_CLEAN_ENABLED)) {
            return $this;
        }

        $this->_errors = array();

        try {
            $this->_log->clean();
        } catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
        }

        $this->_sendLogCleanEmail();

        return $this;
    }
}
