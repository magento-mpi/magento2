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
 * Adminhtml online customers page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Online extends Mage_Adminhtml_Block_Template
{

    protected $_template = 'customer/online.phtml';

    public function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        $this->addChild('filterForm', 'Mage_Adminhtml_Block_Customer_Online_Filter');
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChildBlock('filterForm')->toHtml();
    }

}
