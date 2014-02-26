<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Grid\Column\Renderer;

/**
 * Coupon codes grid "Used" column renderer
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Used
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $value = (int)$row->getData($this->getColumn()->getIndex());
        return empty($value) ? __('No') : __('Yes');
    }
}
