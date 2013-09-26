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
class Magento_Directory_Model_Observer
{
    const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';
    const IMPORT_ENABLE = 'currency/import/enabled';
    const IMPORT_SERVICE = 'currency/import/service';

    const XML_PATH_ERROR_TEMPLATE = 'currency/import/error_email_template';
    const XML_PATH_ERROR_IDENTITY = 'currency/import/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT = 'currency/import/error_email';

    /**
     * @var Magento_Directory_Model_Currency_Import_Factory
     */
    protected $_importFactory;

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
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_emailTemplateFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param Magento_Directory_Model_Currency_Import_Factory $importFactory
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Translate $translate
     * @param Magento_Core_Model_Email_TemplateFactory $emailTemplateFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     */
    public function __construct(
        Magento_Directory_Model_Currency_Import_Factory $importFactory,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Translate $translate,
        Magento_Core_Model_Email_TemplateFactory $emailTemplateFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Model_CurrencyFactory $currencyFactory
    ) {
        $this->_importFactory = $importFactory;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_translate = $translate;
        $this->_emailTemplateFactory = $emailTemplateFactory;
        $this->_storeManager = $storeManager;
        $this->_currencyFactory = $currencyFactory;
    }

    public function scheduledUpdateCurrencyRates($schedule)
    {
        $importWarnings = array();
        if (!$this->_coreStoreConfig->getConfig(self::IMPORT_ENABLE)
            || !$this->_coreStoreConfig->getConfig(self::CRON_STRING_PATH)
        ) {
            return;
        }

        $service = $this->_coreStoreConfig->getConfig(self::IMPORT_SERVICE);
        if( !$service ) {
            $importWarnings[] = __('FATAL ERROR:') . ' ' . __('Please specify the correct Import Service.');
        }

        try {
            $importModel = $this->_importFactory->create($service);
        } catch (Exception $e) {
            $importWarnings[] = __('FATAL ERROR:') . ' ' . __('We can\'t initialize the import model.');
        }

        $rates = $importModel->fetchRates();
        $errors = $importModel->getMessages();

        if( sizeof($errors) > 0 ) {
            foreach ($errors as $error) {
                $importWarnings[] = __('WARNING:') . ' ' . $error;
            }
        }

        if (sizeof($importWarnings) == 0) {
            $this->_currencyFactory->create()->saveRates($rates);
        } else {
            $this->_translate->setTranslateInline(false);

            /* @var $mailTemplate Magento_Core_Model_Email_Template */
            $mailTemplate = $this->_emailTemplateFactory->create();
            $mailTemplate->setDesignConfig(array(
                'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId()
            ))
                ->sendTransactional(
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_TEMPLATE),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_IDENTITY),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings'    => join("\n", $importWarnings),
                )
            );
            $this->_translate->setTranslateInline(true);
        }
    }
}
