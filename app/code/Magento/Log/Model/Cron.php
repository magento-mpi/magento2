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
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Log\Model\Log
     */
    protected $_log;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Log\Model\Log $log
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\TranslateInterface $translate
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Log\Model\Log $log,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\TranslateInterface $translate,
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->_log = $log;
        $this->_storeManager = $storeManager;
        $this->_translate = $translate;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Send Log Clean Warnings
     *
     * @return $this
     */
    protected function _sendLogCleanEmail()
    {
        if (!$this->_errors) {
            return $this;
        }
        if (!$this->_coreStoreConfig->getValue(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
            return $this;
        }

        $this->_translate->setTranslateInline(false);

        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_coreStoreConfig->getValue(self::XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
            ->setTemplateOptions(array(
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId()
            ))
            ->setTemplateVars(array('warnings' => join("\n", $this->_errors)))
            ->setFrom($this->_coreStoreConfig->getValue(self::XML_PATH_EMAIL_LOG_CLEAN_IDENTITY), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
            ->addTo($this->_coreStoreConfig->getValue(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
            ->getTransport();

        $transport->sendMessage();

        $this->_translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Clean logs
     *
     * @return $this
     */
    public function logClean()
    {
        if (!$this->_coreStoreConfig->isSetFlag(self::XML_PATH_LOG_CLEAN_ENABLED, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
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
