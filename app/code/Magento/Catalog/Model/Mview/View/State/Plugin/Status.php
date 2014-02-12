<?php
/**
 * Plugin for \Magento\Mview\View\StateInterface model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Mview\View\State\Plugin;

class Status
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
     * ids list
     *
     * @var array
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

            if ($state->getStatus() == $relatedViewState->getStatus()) {
                return $state;
            }

            $relatedViewState->setStatus($state->getStatus());
            if ($state->getStatus() == \Magento\Mview\View\StateInterface::STATUS_SUSPENDED) {
                $relatedViewState->setVersionId($this->changelog->setViewId($viewId)->getVersion());
            }
            $relatedViewState->save();
        }

        return $state;
    }
}
