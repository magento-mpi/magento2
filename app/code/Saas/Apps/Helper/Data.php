<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Apps
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin apps helper
 *
 * @category   Saas
 * @package    Saas_Apps
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
class Saas_Apps_Helper_Data extends Mage_Backend_Helper_Data
{
    /**
     * XML path to apps urls
     */
    const XML_PATH_APP_TAB_URL = 'default/app_tab_url';

    /**
     * Locale model
     *
     * @var Magento_Core_Model_Locale
     */
    protected $_locale;

    /**
     * Config model
     *
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_config;

    /**
     * Apps helper constructor
     *
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Locale $locale
     */
    public function __construct(
        Magento_Core_Model_Config $config,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Locale $locale
    ) {
        $this->_locale = $locale;
        parent::__construct($config, $context);
    }

    /**
     * Return URL to external applications tab
     *
     * @return string
     */
    public function getAppTabUrl()
    {
        $localeCode = $this->_locale->getLocaleCode();
        if (!$localeCode) {
            $localeCode = Magento_Core_Model_LocaleInterface::DEFAULT_LOCALE;
        }
        $url = (string)$this->_config->getNode(self::XML_PATH_APP_TAB_URL . '/' . $localeCode);
        if (!$url) {
            $url = (string)$this->_config->getNode(
                self::XML_PATH_APP_TAB_URL . '/' . Magento_Core_Model_LocaleInterface::DEFAULT_LOCALE
            );
        }
        return $url;
    }
}
