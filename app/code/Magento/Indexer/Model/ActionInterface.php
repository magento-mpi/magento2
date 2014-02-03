<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

interface ActionInterface
{
    /**
     * Execute full indexation
     */
    public function executeFull();

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     */
    public function executeList($ids);

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     */
    public function executeRow($id);
}
