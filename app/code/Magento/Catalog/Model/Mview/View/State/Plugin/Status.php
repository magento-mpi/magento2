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
     * @var \Magento\Mview\View\StateInterfaceFactory
     */
    protected $stateFactory;

    /**
     * @var \Magento\Mview\View\ChangelogInterfaceFactory
     */
    protected $changelogFactory;

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
     * @param \Magento\Mview\View\StateInterfaceFactory $stateFactory
     * @param \Magento\Mview\View\ChangelogInterfaceFactory $changelogFactory
     */
    public function __construct(
        \Magento\Mview\View\StateInterfaceFactory $stateFactory,
        \Magento\Mview\View\ChangelogInterfaceFactory $changelogFactory
    ) {
        $this->stateFactory = $stateFactory;
        $this->changelogFactory = $changelogFactory;
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

            $relatedViewState = $this->stateFactory->create()
                ->loadByView($viewId);

            if ($state->getStatus() == $relatedViewState->getStatus()) {
                return $state;
            }

            $relatedViewState->setStatus($state->getStatus());
            if ($state->getStatus() == \Magento\Mview\View\StateInterface::STATUS_SUSPENDED) {
                $changelog = $this->changelogFactory->create()->setViewId($viewId);
                $relatedViewState->setVersionId($changelog->getVersion());
            }
            $relatedViewState->save();
        }

        return $state;
    }
}
