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
 * Adminhtml sales order create sidebar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Sales\Order\Create;

class Sidebar extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
{
    protected function _prepareLayout()
    {
        if ($this->getCustomerId()) {
            $button = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')->setData(array(
                'label' => __('Update Changes'),
                'onclick' => 'order.sidebarApplyChanges()',
                'before_html' => '<div class="actions">',
                'after_html' => '</div>'
            ));
            $this->setChild('top_button', $button);
        }

        if ($this->getCustomerId()) {
            $button = clone $button;
            $button->unsId();
            $this->setChild('bottom_button', $button);
        }
        return parent::_prepareLayout();
    }

    public function canDisplay($child)
    {
        if (method_exists($child, 'canDisplay')) {
            return $child->canDisplay();
        }
        return true;
    }
}
