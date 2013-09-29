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
 * Adminhtml customers wishlist grid item renderer for item visibility
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Tab\Wishlist\Grid\Renderer;

class Description extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(\Magento\Object $row)
    {
        return nl2br(htmlspecialchars($row->getData($this->getColumn()->getIndex())));
    }

}
