<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Column renderer for customer email
 */
class Email extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render customer email as mailto link
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    protected function _getValue(\Magento\Framework\Object $row)
    {
        $customerEmail = $this->escapeHtml($row->getData($this->getColumn()->getIndex()));
        return '<a href="mailto:' . $customerEmail . '">' . $this->escapeHtml($customerEmail) . '</a>';
    }
}
