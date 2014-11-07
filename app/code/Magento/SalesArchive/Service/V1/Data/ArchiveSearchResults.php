<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
