<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model\Resource\Indexer\State;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Indexer\Model\Indexer\State', 'Magento\Indexer\Model\Resource\Indexer\State');
    }
}
