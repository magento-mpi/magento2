<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Wishlist\Grid\Renderer;

/**
 * Adminhtml customers wishlist grid item renderer for item visibility
 */
class Description extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the description of given row.
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        return nl2br(htmlspecialchars($row->getData($this->getColumn()->getIndex())));
    }
}
