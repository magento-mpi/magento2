<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional buttons on customer edit form
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Customer\Edit;

class Buttons extends \Magento\Adminhtml\Block\Customer\Edit
{
    /**
     * Add "Manage Shopping Cart" button on customer management page
     *
     * @return \Magento\AdvancedCheckout\Block\Adminhtml\Customer\Edit\Buttons
     */
    public function addButtons()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::view')
            && !$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')
            || \Mage::app()->getStore()->getWebsiteId() == $customer->getWebsiteId()
        ) {
            return $this;
        }
        $container = $this->getParentBlock();
        if ($container instanceof \Magento\Backend\Block\Template && $container->getCustomerId()) {
            $url = \Mage::getSingleton('Magento\Backend\Model\Url')->getUrl('*/checkout/index', array(
                'customer' => $container->getCustomerId()
            ));
            $container->addButton('manage_quote', array(
                'label' => __('Manage Shopping Cart'),
                'onclick' => "setLocation('" . $url . "')",
            ), 0);
        }
        return $this;
    }
}
