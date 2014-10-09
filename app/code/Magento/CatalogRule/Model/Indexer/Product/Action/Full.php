<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Product\Action;

class Full
{
    protected $fullAction;

    /**
     * @param \Magento\CatalogRule\Model\Indexer\Rule\Action\Full $fullAction
     */
    public function __construct(
        \Magento\CatalogRule\Model\Indexer\Rule\Action\Full $fullAction
    ) {
        $this->fullAction = $fullAction;
    }

    /**
     * Full Row reindex
     */
    public function execute()
    {
        $this->fullAction->execute();
    }
}
