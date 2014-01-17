<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

class TestIndexer implements \Magento\Indexer\Model\ActionInterface, \Magento\Mview\ActionInterface
{
    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     */
    public function execute($ids)
    {
        // TODO: Implement execute() method.
    }

    /**
     * Execute full indexation
     */
    public function executeFull()
    {
        // TODO: Implement executeFull() method.
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     */
    public function executeList($ids)
    {
        // TODO: Implement executeList() method.
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     */
    public function executeRow($id)
    {
        // TODO: Implement executeRow() method.
    }
}