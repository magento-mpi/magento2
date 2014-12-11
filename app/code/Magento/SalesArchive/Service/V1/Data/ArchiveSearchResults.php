<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Service\V1\Data;

class ArchiveSearchResults extends \Magento\Framework\Api\SearchResults
{
    /**
     * Returns array of items
     *
     * @return \Magento\SalesArchive\Service\V1\Data\Archive[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
