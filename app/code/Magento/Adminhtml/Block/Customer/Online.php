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
namespace Magento\Adminhtml\Block\Customer;

class Online extends \Magento\Adminhtml\Block\Template
{

    protected $_template = 'customer/online.phtml';

    public function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        $this->addChild('filterForm', '\Magento\Adminhtml\Block\Customer\Online\Filter');
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChildBlock('filterForm')->toHtml();
    }

}
