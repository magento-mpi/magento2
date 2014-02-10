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
     * @var \Magento\Core\Model\Store\Config
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
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param Resource\Sitemap\CollectionFactory $collectionFactory
     * @param \Magento\TranslateInterface $translateModel
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Sitemap\Model\Resource\Sitemap\CollectionFactory $collectionFactory,
        \Magento\TranslateInterface $translateModel,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
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
            $translate = $this->_translateModel->getTranslateInline();
            $this->_translateModel->setTranslateInline(false);

            $this->_transportBuilder
                ->setTemplateIdentifier(
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_TEMPLATE)
                )
                ->setTemplateOptions(array(
                    'area' => \Magento\Core\Model\App\Area::AREA_ADMIN,
                    'store' => $this->_storeManager->getStore()->getId(),
                ))
                ->setTemplateVars(array('warnings' => join("\n", $errors)))
                ->setFrom($this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_IDENTITY))
                ->addTo($this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_RECIPIENT));
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_translateModel->setTranslateInline($translate);
        }
    }
}
