<?php
/**
 * SKU failed description block renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
