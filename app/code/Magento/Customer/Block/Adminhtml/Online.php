<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml;

/**
 * Adminhtml online customers page content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Online extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'online.phtml';

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild('filterForm', 'Magento\Customer\Block\Adminhtml\Online\Filter');
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getFilterFormHtml()
    {
        return $this->getChildBlock('filterForm')->toHtml();
    }
}
