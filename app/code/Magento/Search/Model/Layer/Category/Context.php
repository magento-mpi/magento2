<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\Category;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class Context extends \Magento\Catalog\Model\Layer\Category\Context
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $helper;

    /**
     * @var ItemCollectionProvider
     */
    protected $searchProvider;

    /**
     * @param ItemCollectionProvider $searchProvider
     * @param \Magento\Catalog\Model\Layer\Category\ItemCollectionProvider $catalogProvider
     * @param \Magento\Catalog\Model\Layer\Category\StateKey $stateKey
     * @param \Magento\Catalog\Model\Layer\Category\CollectionFilter $collectionFilter
     * @param \Magento\Search\Helper\Data $helper
     */
    public function __construct(
        ItemCollectionProvider $searchProvider,
        \Magento\Catalog\Model\Layer\Category\ItemCollectionProvider $catalogProvider,
        \Magento\Catalog\Model\Layer\Category\StateKey $stateKey,
        \Magento\Catalog\Model\Layer\Category\CollectionFilter $collectionFilter,
        \Magento\Search\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->searchProvider = $searchProvider;
        parent::__construct($catalogProvider, $stateKey, $collectionFilter);
    }

    /**
     * @return ItemCollectionProviderInterface
     */
    public function getCollectionProvider()
    {
        if ($this->helper->getIsEngineAvailableForNavigation()) {
            return $this->searchProvider;
        }
        return parent::getCollectionProvider();
    }
}
