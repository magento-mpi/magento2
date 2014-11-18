<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * previous implementation @see \Magento\Tax\Service\V1\Data\TaxRuleSearchResults
 */
interface TaxRuleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Magento\Tax\Api\Data\TaxRuleInterface[]
     */
    public function getItems();
}
