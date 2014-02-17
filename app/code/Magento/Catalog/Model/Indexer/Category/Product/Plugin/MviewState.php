<?php
/**
 * Plugin for \Magento\Mview\View\StateInterface model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Product\Plugin;

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
        \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID,
        \Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID
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
            $viewId = $state->getViewId() == \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID
                ? \Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID
                : \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID;

            $relatedViewState = $this->state->loadByView($viewId);

            // if equals nothing to change
            if ($state->getStatus() == $relatedViewState->getStatus()) {
                return $state;
            }

            // suspend
            if ($state->getStatus() == \Magento\Mview\View\StateInterface::STATUS_SUSPENDED) {
                $relatedViewState->setStatus(\Magento\Mview\View\StateInterface::STATUS_SUSPENDED);
                $relatedViewState->setVersionId($this->changelog->setViewId($viewId)->getVersion());
                $relatedViewState->save();
            } else {
                if ($relatedViewState->getStatus() == \Magento\Mview\View\StateInterface::STATUS_SUSPENDED) {
                    $relatedViewState->setStatus(\Magento\Mview\View\StateInterface::STATUS_IDLE);
                    $relatedViewState->save();
                }
            }
        }

        return $state;
    }
}
