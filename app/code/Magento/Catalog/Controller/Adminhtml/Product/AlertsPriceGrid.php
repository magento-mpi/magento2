<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;


class AlertsPriceGrid extends AbstractProductGrid
{
    /**
     * Get alerts price grid
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->resultLayoutFactory->create();
    }
}
