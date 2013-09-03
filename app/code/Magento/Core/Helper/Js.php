<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * JavaScript helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Helper_Js extends Magento_Core_Helper_Abstract
{
    /**
     * Key for cache
     */
    const JAVASCRIPT_TRANSLATE_CONFIG_KEY = 'javascript_translate_config';

    /**
     * Translate file name
     */
    const JAVASCRIPT_TRANSLATE_CONFIG_FILENAME = 'jstranslator.xml';

    /**
     * Array of senteces of JS translations
     *
     * @var array
     */
    protected $_translateData = null;

    /**
     * Translate config
     *
     * @var \Magento\Simplexml\Config
     */
    protected $_config = null;

    /**
     * Modules configuration reader
     *
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_configReader;

    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config_Modules_Reader $configReader
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Model_View_Url $viewUrl
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config_Modules_Reader $configReader,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Model_View_Url $viewUrl
    ) {
        parent::__construct($context);
        $this->_configReader = $configReader;
        $this->_configCacheType = $configCacheType;
        $this->_viewUrl = $viewUrl;
    }

    /**
     * Retrieve JSON of JS sentences translation
     *
     * @return string
     */
    public function getTranslateJson()
    {
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($this->_getTranslateData());
    }

    /**
     * Retrieve JS translator initialization javascript
     *
     * @return string
     */
    public function getTranslatorScript()
    {
        $script = '(function($) {$.mage.translate.add(' . $this->getTranslateJson() . ')})(jQuery);';
        return $this->getScript($script);
    }

    /**
     * Retrieve framed javascript
     *
     * @param   string $script
     * @return  script
     */
    public function getScript($script)
    {
        return '<script type="text/javascript">//<![CDATA[' . "\n{$script}\n" . '//]]></script>';
    }

    /**
     * Retrieve javascript include code
     *
     * @param   string $file
     * @return  string
     */
    public function includeScript($file)
    {
        return '<script type="text/javascript" src="' . $this->_viewUrl->getViewFileUrl($file) . '"></script>' . "\n";
    }

    /**
     * Retrieve JS translation array
     *
     * @return array
     */
    protected function _getTranslateData()
    {
        if ($this->_translateData === null) {
            $this->_translateData = array();
            $messages = $this->_getXmlConfig()->getXpath('*/message');
            if (!empty($messages)) {
                foreach ($messages as $message) {
                    $messageText = (string)$message;
                    $this->_translateData[$messageText] = __($messageText);
                }
            }

            foreach ($this->_translateData as $key => $value) {
                if ($key == $value) {
                    unset($this->_translateData[$key]);
                }
            }
        }
        return $this->_translateData;
    }

    /**
     * Load config from files and try to cache it
     *
     * @return \Magento\Simplexml\Config
     */
    protected function _getXmlConfig()
    {
        if (is_null($this->_config)) {
            $cachedXml = $this->_configCacheType->load(self::JAVASCRIPT_TRANSLATE_CONFIG_KEY);
            if ($cachedXml) {
                $xmlConfig = new \Magento\Simplexml\Config($cachedXml);
            } else {
                $xmlConfig = new \Magento\Simplexml\Config();
                $xmlConfig->loadString('<?xml version="1.0"?><jstranslator></jstranslator>');
                $this->_configReader->loadModulesConfiguration(self::JAVASCRIPT_TRANSLATE_CONFIG_FILENAME, $xmlConfig);
                $this->_configCacheType->save($xmlConfig->getXmlString(), self::JAVASCRIPT_TRANSLATE_CONFIG_KEY);
            }
            $this->_config = $xmlConfig;
        }
        return $this->_config;
    }
}
