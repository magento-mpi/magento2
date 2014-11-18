<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use \Magento\Framework\Api\SearchResults;
use \Magento\Tax\Api\Data\TaxRuleSearchResultsInterface;

class TaxRuleSearchResults extends SearchResults implements TaxRuleSearchResultsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
