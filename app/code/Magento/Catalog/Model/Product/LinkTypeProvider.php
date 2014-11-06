<?php
/**
 * Collection of the available product link types
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

use \Magento\Catalog\Api\Data\ProductLinkTypeInterface as LinkType;
use \Magento\Catalog\Api\Data\ProductLinkAttributeInterface as LinkAttribute;

class LinkTypeProvider implements \Magento\Catalog\Api\ProductLinkTypeListInterface
{
    /**
     * Available product link types
     *
     * Represented by an assoc array with the following format 'product_link_name' => 'product_link_code'
     *
     * @var array
     */
    protected $linkTypes;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkTypeInterfaceBuilder
     */
    protected $linkTypeBuilder;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkAttributeInterfaceBuilder
     */
    protected $linkAttributeBuilder;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $linkFactory;

    /**
     * @param array $linkTypes
     */
    public function __construct(
        \Magento\Catalog\Api\Data\ProductLinkTypeInterfaceBuilder $linkTypeBuilder,
        \Magento\Catalog\Api\Data\ProductLinkAttributeInterfaceBuilder $linkAttributeBuilder,
        \Magento\Catalog\Model\Product\LinkFactory $linkFactory,
        array $linkTypes = array()
    ) {
        $this->linkTypes = $linkTypes;
        $this->linkTypeBuilder = $linkTypeBuilder;
        $this->linkAttributeBuilder = $linkAttributeBuilder;
        $this->linkFactory = $linkFactory;
    }

    /**
     * Retrieve information about available product link types
     *
     * @return array
     */
    public function getLinkTypes()
    {
        return $this->linkTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $output = [];
        foreach ($this->getLinkTypes() as $type => $typeCode) {
            $data = [LinkType::KEY => $type, LinkType::VALUE => $typeCode];
            $output[] = $this->linkTypeBuilder
                ->populateWithArray($data)
                ->create();
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemAttributes($type)
    {
        $output = [];
        $types = $this->getLinkTypes();
        $typeId = isset($types[$type]) ? $types[$type] : null;

        /** @var \Magento\Catalog\Model\Product\Link $link */
        $link = $this->linkFactory->create(['data' => ['link_type_id' => $typeId]]);
        $attributes = $link->getAttributes();
        foreach ($attributes as $item) {
            $data = [
                LinkAttribute::KEY => $item['code'],
                LinkAttribute::VALUE => $item['type'],
            ];
            $output[] = $this->linkAttributeBuilder->populateWithArray($data)->create();
        }
        return $output;
    }
}
