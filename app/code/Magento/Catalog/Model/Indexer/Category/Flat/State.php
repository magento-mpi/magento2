<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat;

class State extends \Magento\Catalog\Model\Indexer\AbstractFlatState
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_category_flat';

    /**
     * Flat Is Enabled Config XML Path
     */
    const INDEXER_ENABLED_XML_PATH = 'catalog/frontend/flat_catalog_category';
}
