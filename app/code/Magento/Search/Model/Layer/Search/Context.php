<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\Search;

use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\Layer\ContextInterface;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\Layer\StateKeyInterface;

class Context implements ContextInterface
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $helper;

    /**
     * @var ItemCollectionProvider
     */
    protected $collectionProvider;

    public function __construct(
        \Magento\Catalog\Model\Layer\Search\Context $catalogContext,
        ItemCollectionProvider $collectionProvider,
        \Magento\Search\Helper\Data $helper
    ) {
        $this->catalogContext = $catalogContext;
        $this->collectionProvider = $collectionProvider;
        $this->helper = $helper;
    }

    /**
     * @return ItemCollectionProviderInterface
     */
    public function getCollectionProvider()
    {
        if ($this->helper->isThirdPartSearchEngine() && $this->helper->isActiveEngine()) {
            return $this->collectionProvider;
        }
        return $this->catalogContext->getCollectionProvider();
    }

    /**
     * @return StateKeyInterface
     */
    public function getStateKey()
    {
        return $this->catalogContext->getStateKey();
    }

    /**
     * @return CollectionFilterInterface
     */
    public function getCollectionFilter()
    {
        return $this->catalogContext->getCollectionFilter();
    }
} 
