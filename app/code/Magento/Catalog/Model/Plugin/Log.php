<?php
/**
 * Plugin for \Magento\Log\Model\Resource\Log model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Plugin;

class Log
{
    /**
     * @var \Magento\Catalog\Model\Product\Compare\Item
     */
    protected $_productCompareItem;

    /**
     * @param \Magento\Catalog\Model\Product\Compare\Item $productCompareItem
     */
    public function __construct(\Magento\Catalog\Model\Product\Compare\Item $productCompareItem)
    {
        $this->_productCompareItem = $productCompareItem;
    }

    /**
     * Catalog Product Compare Items Clean
     * after plugin for clean method
     *
     * @param \Magento\Log\Model\Resource\Log $logResourceModel
     * @return \Magento\Log\Model\Resource\Log
     */
    public function afterClean($logResourceModel)
    {
        $this->_productCompareItem->clean();
        return $logResourceModel;
    }
}
