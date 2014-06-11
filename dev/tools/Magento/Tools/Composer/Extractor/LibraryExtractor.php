<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use Magento\Tools\Composer\Model\Package;

/**
 * Extractor for Library
 */
class LibraryExtractor extends  FrameworkExtractor
{

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '/lib/internal';
    }

    /**
     * Iterate through one location instead of multiple locations
     *
     * @param array $collection
     * @param int &$count
     * @return array
     */
    public function extract(array $collection = array(), &$count = 0)
    {
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);
        $definition = $this->_parser->getMappings($this->_rootDir, $this->getPath());
        $this->create($definition);
        return $this->_collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-library";
    }



}