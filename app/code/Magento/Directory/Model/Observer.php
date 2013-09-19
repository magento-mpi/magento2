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
 * \Directory module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model;

class Observer
{
    const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';
    const IMPORT_ENABLE = 'currency/import/enabled';
    const IMPORT_SERVICE = 'currency/import/service';

    const XML_PATH_ERROR_TEMPLATE = 'currency/import/error_email_template';
    const XML_PATH_ERROR_IDENTITY = 'currency/import/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT = 'currency/import/error_email';

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreConfig = $coreConfig;
    }

    public function scheduledUpdateCurrencyRates($schedule)
    {
        $importWarnings = array();
        if(!$this->_coreStoreConfig->getConfig(self::IMPORT_ENABLE) || !$this->_coreStoreConfig->getConfig(self::CRON_STRING_PATH)) {
            return;
        }

        $service = $this->_coreStoreConfig->getConfig(self::IMPORT_SERVICE);
        if( !$service ) {
            $importWarnings[] = __('FATAL ERROR:') . ' ' . __('Please specify the correct Import Service.');
        }

        try {
            $importModel = \Mage::getModel(
                $this->_coreConfig->getNode('global/currency/import/services/' . $service . '/model')->asArray()
            );
        } catch (\Exception $e) {
            $importWarnings[] = __('FATAL ERROR:') . ' ' . \Mage::throwException(__("We can't initialize the import model."));
        }

        $rates = $importModel->fetchRates();
        $errors = $importModel->getMessages();

        if( sizeof($errors) > 0 ) {
            foreach ($errors as $error) {
                $importWarnings[] = __('WARNING:') . ' ' . $error;
            }
        }

        if (sizeof($importWarnings) == 0) {
            \Mage::getModel('Magento\Directory\Model\Currency')->saveRates($rates);
        }
        else {
            $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
            /* @var $translate \Magento\Core\Model\Translate */
            $translate->setTranslateInline(false);

            /* @var $mailTemplate \Magento\Core\Model\Email\Template */
            $mailTemplate = \Mage::getModel('Magento\Core\Model\Email\Template');
            $mailTemplate->setDesignConfig(array(
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                'store' => \Mage::app()->getStore()->getId()
            ))
                ->sendTransactional(
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_TEMPLATE),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_IDENTITY),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings'    => join("\n", $importWarnings),
                )
            );

            $translate->setTranslateInline(true);
        }
    }
}
