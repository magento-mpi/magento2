<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Wishlist\Grid\Renderer;

/**
 * Adminhtml customers wishlist grid item renderer for item visibility
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Description extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        return nl2br(htmlspecialchars($row->getData($this->getColumn()->getIndex())));
    }

}
