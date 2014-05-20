<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

use \Magento\Catalog\Model\Product\LinkTypeProvider;
use \Magento\Catalog\Service\V1\Data\CatalogProductLinkBuilder as Builder;
use \Magento\Catalog\Service\V1\Data\CatalogProductLink;

class CatalogProductLinkService implements CatalogProductLinkServiceInterface
{
    /**
     * @var LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param LinkTypeProvider $linkTypeProvider
     * @param Builder $builder
     */
    public function __construct(
        LinkTypeProvider $linkTypeProvider,
        Builder $builder
    ) {
        $this->linkTypeProvider = $linkTypeProvider;
        $this->builder = $builder;
    }

    /**
     * Provide the list of product link types
     *
     * @return \Magento\Catalog\Service\V1\Data\CatalogProductLink[]
     */
    public function getProductLinkTypes()
    {
        $output = [];
        foreach ($this->linkTypeProvider->getLinkTypes() as $type => $typeCode) {
            $data = [CatalogProductLink::TYPE => $type, CatalogProductLink::CODE => $typeCode];
            $output[] = $this->builder
                ->populateWithArray($data)
                ->create();
        }
        return $output;
    }
}
