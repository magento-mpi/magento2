<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model;

class Observer
{
    const CRON_STRING_PATH = 'crontab/default/jobs/currency_rates_update/schedule/cron_expr';
    const IMPORT_ENABLE = 'currency/import/enabled';
    const IMPORT_SERVICE = 'currency/import/service';

    const XML_PATH_ERROR_TEMPLATE = 'currency/import/error_email_template';
    const XML_PATH_ERROR_IDENTITY = 'currency/import/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT = 'currency/import/error_email';

    /**
     * @var \Magento\Directory\Model\Currency\Import\Factory
     */
    protected $_importFactory;

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
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param \Magento\Directory\Model\Currency\Import\Factory $importFactory
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param \Magento\TranslateInterface $translate
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     */
    public function __construct(
        \Magento\Directory\Model\Currency\Import\Factory $importFactory,
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\TranslateInterface $translate,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
        $this->_importFactory = $importFactory;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_translate = $translate;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_currencyFactory = $currencyFactory;
    }

    /**
     * @param mixed $schedule
     * @return void
     */
    public function scheduledUpdateCurrencyRates($schedule)
    {
        $importWarnings = array();
        if (!$this->_coreStoreConfig->getValue(self::IMPORT_ENABLE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
            || !$this->_coreStoreConfig->getValue(self::CRON_STRING_PATH, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
        ) {
            return;
        }

        $errors = array();
        $rates = array();
        $service = $this->_coreStoreConfig->getValue(self::IMPORT_SERVICE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
        if ($service) {
            try {
                $importModel = $this->_importFactory->create($service);
                $rates = $importModel->fetchRates();
                $errors = $importModel->getMessages();
            } catch (\Exception $e) {
                $importWarnings[] = __('FATAL ERROR:') . ' ' . __('We can\'t initialize the import model.');
            }
        } else {
            $importWarnings[] = __('FATAL ERROR:') . ' ' . __('Please specify the correct Import Service.');
        }

        if (sizeof($errors) > 0) {
            foreach ($errors as $error) {
                $importWarnings[] = __('WARNING:') . ' ' . $error;
            }
        }

        if (sizeof($importWarnings) == 0) {
            $this->_currencyFactory->create()->saveRates($rates);
        } else {
            $translate = $this->_translate->getTranslateInline();
            $this->_translate->setTranslateInline(false);

            $this->_transportBuilder->setTemplateIdentifier(
                    $this->_coreStoreConfig->getValue(self::XML_PATH_ERROR_TEMPLATE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
                )
                ->setTemplateOptions(array(
                    'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ))
                ->setTemplateVars(array('warnings' => join("\n", $importWarnings)))
                ->setFrom($this->_coreStoreConfig->getValue(self::XML_PATH_ERROR_IDENTITY), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
                ->addTo($this->_coreStoreConfig->getValue(self::XML_PATH_ERROR_RECIPIENT), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_translate->setTranslateInline($translate);
        }
    }
}
