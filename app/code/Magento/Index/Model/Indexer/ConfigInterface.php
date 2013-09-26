<?php
/**
 * Indexer configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Indexer;

interface ConfigInterface
{
    /**
     * Get indexer data by name
     *
     * @param string $name
     * @return array
     */
    public function getIndexer($name);

    /**
     * Get indexers configuration
     *
     * @return array
     */
    public function getAll();
}
