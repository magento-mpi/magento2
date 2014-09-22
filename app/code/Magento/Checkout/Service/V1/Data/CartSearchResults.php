<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

/**
 * @codeCoverageIgnore
 */
class CartSearchResults extends \Magento\Framework\Service\V1\Data\SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Checkout\Service\V1\Data\Cart[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
