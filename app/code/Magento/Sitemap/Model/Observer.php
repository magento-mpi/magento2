<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Model;

/**
 * Sitemap module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Sitemap\Model\Resource\Sitemap\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translateModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param Resource\Sitemap\CollectionFactory $collectionFactory
     * @param \Magento\TranslateInterface $translateModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\Sitemap\Model\Resource\Sitemap\CollectionFactory $collectionFactory,
        \Magento\TranslateInterface $translateModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_collectionFactory = $collectionFactory;
        $this->_translateModel = $translateModel;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
    }

    /**
     * Generate sitemaps
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return void
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = array();

        // check if scheduled generation enabled
        if (!$this->_coreStoreConfig->isSetFlag(self::XML_PATH_GENERATION_ENABLED, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
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

        if ($errors && $this->_coreStoreConfig->getValue(self::XML_PATH_ERROR_RECIPIENT, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
            $translate = $this->_translateModel->getTranslateInline();
            $this->_translateModel->setTranslateInline(false);

            $this->_transportBuilder
                ->setTemplateIdentifier(
                    $this->_coreStoreConfig->getValue(self::XML_PATH_ERROR_TEMPLATE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
                )
                ->setTemplateOptions(array(
                    'area' => \Magento\Core\Model\App\Area::AREA_ADMIN,
                    'store' => $this->_storeManager->getStore()->getId(),
                ))
                ->setTemplateVars(array('warnings' => join("\n", $errors)))
                ->setFrom($this->_coreStoreConfig->getValue(self::XML_PATH_ERROR_IDENTITY), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
                ->addTo($this->_coreStoreConfig->getValue(self::XML_PATH_ERROR_RECIPIENT), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_translateModel->setTranslateInline($translate);
        }
    }
}
