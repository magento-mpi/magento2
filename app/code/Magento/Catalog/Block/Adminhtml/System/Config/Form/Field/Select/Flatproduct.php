<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Config Field Select Flat Product Block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Adminhtml_System_Config_Form_Field_Select_Flatproduct
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Catalog product flat
     *
     * @var Magento_Catalog_Helper_Product_Flat
     */
    protected $_catalogProductFlat = null;

    /**
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_catalogProductFlat = $catalogProductFlat;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Retrieve Element HTML
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element) {
        if (!$this->_catalogProductFlat->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }
        return parent::_getElementHtml($element);
    }

}
