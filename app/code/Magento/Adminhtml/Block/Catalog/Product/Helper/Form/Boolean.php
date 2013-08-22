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
 * Product form boolean field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean extends Magento_Data_Form_Element_Select
{
    protected function _construct()
    {
        parent::_construct();
        $this->setValues(array(
            array(
                'label' => __('No'),
                'value' => 0,
            ),
            array(
                'label' => __('Yes'),
                'value' => 1,
            ),
        ));
    }
}
