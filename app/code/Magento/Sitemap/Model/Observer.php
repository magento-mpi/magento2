<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap module observer
 *
 * @category   Magento
 * @package    Magento_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sitemap_Model_Observer
{

    /**
     * Enable/disable configuration
     */
    const XML_PATH_GENERATION_ENABLED = 'sitemap/generate/enabled';

    /**
     * Cronjob expression configuration
     */
    const XML_PATH_CRON_EXPR = 'crontab/jobs/generate_sitemaps/schedule/cron_expr';

    /**
     * Error email template configuration
     */
    const XML_PATH_ERROR_TEMPLATE  = 'sitemap/generate/error_email_template';

    /**
     * Error email identity configuration
     */
    const XML_PATH_ERROR_IDENTITY  = 'sitemap/generate/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    const XML_PATH_ERROR_RECIPIENT = 'sitemap/generate/error_email';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Sitemap_Model_Resource_Sitemap_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translateModel;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Sitemap_Model_Resource_Sitemap_CollectionFactory $collectionFactory
     * @param Magento_Core_Model_Translate $translateModel
     * @param Magento_Core_Model_Email_TemplateFactory $templateFactory
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Sitemap_Model_Resource_Sitemap_CollectionFactory $collectionFactory,
        Magento_Core_Model_Translate $translateModel,
        Magento_Core_Model_Email_TemplateFactory $templateFactory
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_collectionFactory = $collectionFactory;
        $this->_translateModel = $translateModel;
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Generate sitemaps
     *
     * @param Magento_Cron_Model_Schedule $schedule
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = array();

        // check if scheduled generation enabled
        if (!$this->_coreStoreConfig->getConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }

        $collection = $this->_collectionFactory->create();
        /* @var $collection Magento_Sitemap_Model_Resource_Sitemap_Collection */
        foreach ($collection as $sitemap) {
            /* @var $sitemap Magento_Sitemap_Model_Sitemap */

            try {
                $sitemap->generateXml();
            }
            catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $this->_translateModel->setTranslateInline(false);

            $emailTemplate = $this->_templateFactory->create();
            /* @var $emailTemplate Magento_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_TEMPLATE),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_IDENTITY),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $errors))
                );

            $this->_translateModel->setTranslateInline(true);
        }
    }
}
