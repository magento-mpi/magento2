<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Directory_Block_Adminhtml_Frontend_Region_Updater
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Directory data
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryData = null;

    /**
     * @param Magento_Directory_Helper_Data $directoryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_Directory_Helper_Data $directoryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($coreData, $context, $application, $data);
    }

    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
        $html .= "<script type=\"text/javascript\">var updater = new RegionUpdater('tax_defaults_country',"
            . " 'tax_region', 'tax_defaults_region', "
            . $this->_directoryData->getRegionJson()
            . ", 'disable');</script>";

        return $html;
    }
}



