<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer;

interface ContextInterface
{
    /**
     * @return ItemCollectionProviderInterface
     */
    public function getCollectionProvider();

    /**
     * @return StateKeyInterface
     */
    public function getStateKey();

    /**
     * @return CollectionFilterInterface
     */
    public function getCollectionFilter();
}
