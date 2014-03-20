<?php
/**
 * Plugin for \Magento\Mview\View\StateInterface model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class MviewState
{
    /**
     * @var \Magento\Mview\View\StateInterface
     */
    protected $state;

    /**
     * @var \Magento\Mview\View\ChangelogInterface
     */
    protected $changelog;

    /**
     * Related indexers IDs
     *
     * @var int[]
     */
    protected $viewIds = array(
        \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
        \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID
    );

    /**
     * @param \Magento\Mview\View\StateInterface $state
     * @param \Magento\Mview\View\ChangelogInterface $changelog
     */
    public function __construct(
        \Magento\Mview\View\StateInterface $state,
        \Magento\Mview\View\ChangelogInterface $changelog
    ) {
        $this->state = $state;
        $this->changelog = $changelog;
    }

    /**
     * Synchronize status for view
     *
     * @param \Magento\Mview\View\StateInterface $state
     * @return \Magento\Mview\View\StateInterface
     */
    public function afterSetStatus(\Magento\Mview\View\StateInterface $state)
    {
        if (in_array($state->getViewId(), $this->viewIds)) {
            $viewId = $state->getViewId() ==
                \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID ? \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID : \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID;

            $relatedState = $this->state->loadByView($viewId);

            // If equals nothing to change
            if ($relatedState->getMode() == \Magento\Mview\View\StateInterface::MODE_DISABLED ||
                $state->getStatus() == $relatedState->getStatus()
            ) {
                return $state;
            }

            // Suspend
            if ($state->getStatus() == \Magento\Mview\View\StateInterface::STATUS_SUSPENDED) {
                $relatedState->setStatus(\Magento\Mview\View\StateInterface::STATUS_SUSPENDED);
                $relatedState->setVersionId($this->changelog->setViewId($viewId)->getVersion());
                $relatedState->save();
            } else {
                if ($relatedState->getStatus() == \Magento\Mview\View\StateInterface::STATUS_SUSPENDED) {
                    $relatedState->setStatus(\Magento\Mview\View\StateInterface::STATUS_IDLE);
                    $relatedState->save();
                }
            }
        }

        return $state;
    }
}
