<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Category;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class Context extends \Magento\Catalog\Model\Layer\Context
{
    /**
     * @var \Magento\Solr\Helper\Data
     */
    protected $helper;

    /**
     * @var ItemCollectionProvider
     */
    protected $searchProvider;

    /**
     * @param \Magento\Catalog\Model\Layer\Category\ItemCollectionProvider $collectionProvider
     * @param \Magento\Catalog\Model\Layer\Category\StateKey $stateKey
     * @param \Magento\Catalog\Model\Layer\Category\CollectionFilter $collectionFilter
     * @param ItemCollectionProvider $searchProvider
     * @param \Magento\Solr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Category\ItemCollectionProvider $collectionProvider,
        \Magento\Catalog\Model\Layer\Category\StateKey $stateKey,
        \Magento\Catalog\Model\Layer\Category\CollectionFilter $collectionFilter,
        ItemCollectionProvider $searchProvider,
        \Magento\Solr\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->searchProvider = $searchProvider;
        parent::__construct($collectionProvider, $stateKey, $collectionFilter);
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
