<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Media library js helper
 *
 * @deprecated since 1.7.0.0
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Helper_Media_Js extends Magento_Core_Helper_Js
{
    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config_Modules_Reader $configReader
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config_Modules_Reader $configReader,
        Magento_Core_Model_Cache_Type_Config $configCacheType
    )
    {
        parent::__construct($context, $configReader, $configCacheType);
        $this->_translateData = array(
            'Complete' => __('Complete'),
            'The file size should be more than 0 bytes.' => __('The file size should be more than 0 bytes.'),
            'Upload Security Error' => __('Upload Security Error'),
            'Upload HTTP Error'     => __('Upload HTTP Error'),
            'Upload I/O Error'     => __('Upload I/O Error'),
            'SSL Error: Invalid or self-signed certificate' => __('SSL Error: Invalid or self-signed certificate'),
            'Tb' => __('Tb'),
            'Gb' => __('Gb'),
            'Mb' => __('Mb'),
            'Kb' => __('Kb'),
            'b' => __('b')
        );
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

}
