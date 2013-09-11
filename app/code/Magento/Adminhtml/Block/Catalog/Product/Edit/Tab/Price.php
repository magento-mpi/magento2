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
 * Adminhtml product edit price block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab;

class Price extends \Magento\Adminhtml\Block\Widget\Form
{
    protected function _prepareForm()
    {
        $product = \Mage::registry('product');

        $form = new \Magento\Data\Form();
        $fieldset = $form->addFieldset('tiered_price', array('legend' => __('Tier Pricing')));

        $fieldset->addField('default_price', 'label', array(
                'label'=> __('Default Price'),
                'title'=> __('Default Price'),
                'name'=>'default_price',
                'bold'=>true,
                'value'=>$product->getPrice()
        ));

        $fieldset->addField('tier_price', 'text', array(
                'name'=>'tier_price',
                'class'=>'requried-entry',
                'value'=>$product->getData('tier_price')
        ));

        $form->getElement('tier_price')->setRenderer(
            $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Price\Tier')
        );

        $this->setForm($form);
    }
}// Class \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Price END
