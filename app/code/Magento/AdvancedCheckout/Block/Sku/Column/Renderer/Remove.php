<?php
/**
 * SKU failed information block renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Sku\Column\Renderer;

class Remove extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Button
{
    public function render(\Magento\Object $row)
    {
        $removeButtonHtml = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button', '', array(
            'data' => array(
                'class' => 'delete',
                'label' => 'Remove',
                'onclick' => 'addBySku.removeFailedItem(this)',
                'type' => 'button',
            )
        ));

        return $removeButtonHtml->toHtml();
    }
}
