<?php
namespace Magento\SalesArchive\Service\V1\Data;

class ArchiveSearchResults extends \Magento\Framework\Service\V1\Data\SearchResults
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
