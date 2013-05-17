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
     * @var Mage_Core_Model_Locale
     */
    protected $_locale;

    /**
     * Config model
     *
     * @var Mage_Core_Model_Config_Modules
     */
    protected $_config;

    /**
     * Apps helper constructor
     *
     * @param Mage_Core_Model_Config_Modules $config
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Locale $locale
     */
    public function __construct(
        Mage_Core_Model_Config_Modules $config,
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Locale $locale
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
            $localeCode = Mage_Core_Model_Locale::DEFAULT_LOCALE;
        }
        $url = (string)$this->_config->getNode(self::XML_PATH_APP_TAB_URL . '/' . $localeCode);
        if (!$url) {
            $url = (string)$this->_config->getNode(
                self::XML_PATH_APP_TAB_URL . '/' . Mage_Core_Model_Locale::DEFAULT_LOCALE
            );
        }
        return $url;
    }
}
