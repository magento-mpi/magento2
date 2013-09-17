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
 * Adminhtml report review product blocks content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Review_Product extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'report_review_product';
        $this->_headerText = __('Products Reviews');
        parent::_construct();
        $this->_removeButton('add');
    }

}
