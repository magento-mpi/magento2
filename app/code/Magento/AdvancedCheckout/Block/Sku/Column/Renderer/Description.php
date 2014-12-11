<?php
/**
 * SKU failed description block renderer
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Block\Sku\Column\Renderer;

class Description extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $descriptionBlock = $this->getLayout()->createBlock(
            'Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\Description',
            '',
            ['data' => ['product' => $row->getProduct(), 'item' => $row]]
        );

        return $descriptionBlock->toHtml();
    }
}
