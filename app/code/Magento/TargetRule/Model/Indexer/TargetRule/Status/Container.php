<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Status;

class Container
{
    /**
     * @var array
     */
    protected $_fullReindexPassed = [];

    /**
     * Set indexer full reindex was passed
     *
     * @param string $indexerIdString
     *
     * @return void
     */
    public function setFullReindexPassed($indexerIdString)
    {
        $this->_fullReindexPassed[$indexerIdString] = true;
    }

    /**
     * Get indexer full reindex was passed
     *
     * @param string $indexerIdString
     * @return bool
     */
    public function getFullReindexPassed($indexerIdString)
    {
        return isset($this->_fullReindexPassed[$indexerIdString]) ? $this->_fullReindexPassed[$indexerIdString] : false;
    }

    /**
     * Is full reindex for specified indexer passed
     *
     * @param string $indexerIdString
     * @return bool
     */
    public function isFullReindexPassed($indexerIdString)
    {
        return $this->getFullReindexPassed($indexerIdString) === true;
    }
}
