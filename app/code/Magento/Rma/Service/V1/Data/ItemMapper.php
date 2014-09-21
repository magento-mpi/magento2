<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

/**
 * Mapper class for \Magento\Rma\Service\V1\Data\Item
 */
class ItemMapper
{
    /**
     * itemBuilder
     *
     * @var ItemBuilder
     */
    protected $itemBuilder = null;

    /**
     * Mapper constructor
     *
     * @param ItemBuilder $itemBuilder
     */
    public function __construct(ItemBuilder $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Extract data object from model
     *
     * @param \Magento\Rma\Model\Item $item
     * @return \Magento\Rma\Service\V1\Data\Item
     */
    public function extractDto(\Magento\Rma\Model\Item $item)
    {
        $attributes = array();
        foreach ($item->getAttributes() as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $value = $item->getDataUsingMethod($attrCode);
            $value = $value ? $value : $item->getData($attrCode);
            if (null !== $value) {
                if ($attrCode == 'entity_id') {
                    $attributes[Item::ID] = $value;
                } else {
                    $attributes[$attrCode] = $value;
                }
            }
        }

        $this->itemBuilder->populateWithArray(array_merge($attributes, $item->getData()));
        return $this->itemBuilder->create();
    }
}
