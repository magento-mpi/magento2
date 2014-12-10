<?php
/**
 * SKU failed information block renderer
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Block\Sku\Column\Renderer;

class Remove extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Button
{
    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $removeButtonHtml = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button',
            '',
            [
                'data' => [
                    'class' => 'delete',
                    'label' => 'Remove',
                    'onclick' => 'addBySku.removeFailedItem(this)',
                    'type' => 'button',
                ]
            ]
        );

        return $removeButtonHtml->toHtml();
    }
}
