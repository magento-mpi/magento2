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
namespace Magento\Sitemap\Model;

class Observer
{

    /**
     * Enable/disable configuration
     */
    const XML_PATH_GENERATION_ENABLED = 'sitemap/generate/enabled';

    /**
     * Cronjob expression configuration
     */
    const XML_PATH_CRON_EXPR = 'crontab/default/jobs/generate_sitemaps/schedule/cron_expr';

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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Sitemap\Model\Resource\Sitemap\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translateModel;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Sitemap\Model\Resource\Sitemap\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\Translate $translateModel
     * @param \Magento\Email\Model\TemplateFactory $templateFactory
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Sitemap\Model\Resource\Sitemap\CollectionFactory $collectionFactory,
        \Magento\Core\Model\Translate $translateModel,
        \Magento\Email\Model\TemplateFactory $templateFactory
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_collectionFactory = $collectionFactory;
        $this->_translateModel = $translateModel;
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Generate sitemaps
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = array();

        // check if scheduled generation enabled
        if (!$this->_coreStoreConfig->getConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }

        $collection = $this->_collectionFactory->create();
        /* @var $collection \Magento\Sitemap\Model\Resource\Sitemap\Collection */
        foreach ($collection as $sitemap) {
            /* @var $sitemap \Magento\Sitemap\Model\Sitemap */

            try {
                $sitemap->generateXml();
            }
            catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $this->_translateModel->setTranslateInline(false);

            $emailTemplate = $this->_templateFactory->create();
            /* @var $emailTemplate \Magento\Email\Model\Template */
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
