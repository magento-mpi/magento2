<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\OfflineShipping\Model\Observer\SalesRule;

use Magento\OfflineShipping\Model\SalesRule\Rule;

/**
 * Checkout cart shipping block plugin
 *
 * @category  Magento
 * @package   Magento_OfflineShipping
 * @author    Magento Core Team <core@magentocommerce.com>
 */
class ActionsTab
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function prepareForm($observer)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $observer->getForm();
        foreach ($form->getElements() as $element) {
            /** @var \Magento\Framework\Data\Form\Element\AbstractElement $element */
            if ($element->getId() == 'action_fieldset') {
                $element->addField(
                    'simple_free_shipping',
                    'select',
                    array(
                        'label' => __('Free Shipping'),
                        'title' => __('Free Shipping'),
                        'name' => 'simple_free_shipping',
                        'options' => array(
                            0 => __('No'),
                            Rule::FREE_SHIPPING_ITEM => __('For matching items only'),
                            Rule::FREE_SHIPPING_ADDRESS => __('For shipment with matching items')
                        )
                    )
                );
            }
        }
    }
}
