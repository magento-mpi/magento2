<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Media library js helper
 *
 * @deprecated since 1.7.0.0
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Media_Js extends Mage_Core_Helper_Js
{
    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config_Modules_Reader $configReader
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config_Modules_Reader $configReader,
        Mage_Core_Model_Cache_Type_Config $configCacheType
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
