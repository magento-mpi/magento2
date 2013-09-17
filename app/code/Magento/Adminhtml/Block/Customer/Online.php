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
 * Adminhtml online customers page content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Online extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'customer/online.phtml';

    public function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        $this->addChild('filterForm', 'Magento_Adminhtml_Block_Customer_Online_Filter');
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChildBlock('filterForm')->toHtml();
    }

}
