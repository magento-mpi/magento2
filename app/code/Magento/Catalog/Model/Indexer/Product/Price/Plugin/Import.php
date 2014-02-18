<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

class Import extends AbstractPlugin
{
    /**
     * After import handler
     *
     * @param Object $import
     * @return Object
     */
    public function afterImportSource($import)
    {
        $this->invalidateIndexer();
        return $import;
    }
}
