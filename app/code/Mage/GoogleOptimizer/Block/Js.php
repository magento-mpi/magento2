<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Optimizer Block with additional js scripts in template
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Js extends Mage_Adminhtml_Block_Template
{
    public function getJsonConversionPagesUrl()
    {
        return Mage::helper('Mage_GoogleOptimizer_Helper_Data')->getConversionPagesUrl()->toJson();
    }

    public function getMaxCountOfAttributes()
    {
        return Mage_GoogleOptimizer_Model_Code_Product::DEFAULT_COUNT_OF_ATTRIBUTES;
    }

    public function getExportUrl()
    {
        return $this->getUrl('*/googleoptimizer_index/codes');
    }

    public function getControlFieldKey ()
    {
        return $this->getDataSetDefault('control_field_key', 'control_script');
    }

    public function getTrackingFieldKey ()
    {
        return $this->getDataSetDefault('tracking_field_key', 'tracking_script');
    }

    public function getConversionFieldKey ()
    {
        return $this->getDataSetDefault('conversion_field_key', 'conversion_script');
    }
}
