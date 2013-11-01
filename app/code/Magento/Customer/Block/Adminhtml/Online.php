<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml online customers page content block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml;

class Online extends \Magento\Adminhtml\Block\Template
{

    protected $_template = 'online.phtml';

    public function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    protected function _prepareLayout()
    {
        $this->addChild('filterForm', 'Magento\Customer\Block\Adminhtml\Online\Filter');
        return parent::_prepareLayout();
    }

    public function getFilterFormHtml()
    {
        return $this->getChildBlock('filterForm')->toHtml();
    }

}
