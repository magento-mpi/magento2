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
 * Coupon codes grid "Used" column renderer
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Coupons\Grid\Column\Renderer;

class Used
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Text
{
    public function render(\Magento\Object $row)
    {
        $value = (int)$row->getData($this->getColumn()->getIndex());
        return empty($value) ? __('No') : __('Yes');
    }
}
