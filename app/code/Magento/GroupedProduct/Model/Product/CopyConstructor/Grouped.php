<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\CopyConstructor;

class Grouped implements \Magento\Catalog\Model\Product\CopyConstructorInterface
{
    /**
     * Retrieve collection grouped link
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Resource\Product\Link\Collection
     */
    protected function getGroupedLinkCollection(\Magento\Catalog\Model\Product $product)
    {
        /** @var \Magento\Catalog\Model\Product\Link  $links */
        $links = $product->getLinkInstance();
        $links->setLinkTypeId(\Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED);

        $collection = $links->getLinkCollection();
        $collection->setProduct($product);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Build product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $duplicate
     */
    public function build(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Product $duplicate)
    {
        if ($product->getTypeId() !== \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            //do nothing if not grouped product
            return;
        }

        $data = array();
        $attributes = array();
        $product->getLinkInstance()
            ->setLinkTypeId(\Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED);
        foreach ($product->getLinkInstance()->getAttributes() as $attribute) {
            if (isset($attribute['code'])) {
                $attributes[] = $attribute['code'];
            }
        }
        /** @var \Magento\Catalog\Model\Product\Link $link  */
        foreach ($this->getGroupedLinkCollection($product) as $link) {
            $data[$link->getLinkedProductId()] = $link->toArray($attributes);
        }
        $duplicate->setGroupedLinkData($data);
    }
}
