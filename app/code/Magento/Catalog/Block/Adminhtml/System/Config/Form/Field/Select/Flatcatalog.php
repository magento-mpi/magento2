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
 * System configuration shipping methods allow all countries selec
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Adminhtml_System_Config_Form_Field_Select_Flatcatalog
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Catalog category flat
     *
     * @var Magento_Catalog_Helper_Category_Flat
     */
    protected $_catalogCategoryFlat = null;

    /**
     * @param Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_catalogCategoryFlat = $catalogCategoryFlat;
        parent::__construct($coreData, $context, $application, $data);
    }

    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        if (!$this->_catalogCategoryFlat->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }
        return parent::_getElementHtml($element);
    }

}
