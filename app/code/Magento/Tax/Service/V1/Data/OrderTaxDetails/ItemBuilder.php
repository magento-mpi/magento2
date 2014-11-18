<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\OrderTaxDetails;

use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Api\MetadataServiceInterface;

/**
 * Builder for the Item Data Object
 *
 * @method Item create()
 * @derecated
 * @use \Magento\Tax\Api\Data\OrderTaxDetailsItemDataBuilder
 */
class ItemBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Applied Tax data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\OrderTaxDetails\AppliedTaxBuilder
     */
    protected $appliedTaxBuilder;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeDataBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param AppliedTaxBuilder $appliedTaxBuilder
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeDataBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        AppliedTaxBuilder $appliedTaxBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->appliedTaxBuilder = $appliedTaxBuilder;
    }

    /**
     * Set type (shipping, product, weee, gift wrapping, etc.)
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->_set(Item::KEY_TYPE, $type);
        return $this;
    }

    /**
     * Set item id
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        $this->_set(Item::KEY_ITEM_ID, $itemId);
        return $this;
    }

    /**
     * Set associated item id
     *
     * @param int $associatedItemId
     * @return $this
     */
    public function setAssociatedItemId($associatedItemId)
    {
        $this->_set(Item::KEY_ASSOCIATED_ITEM_ID, $associatedItemId);
        return $this;
    }

    /**
     * Set applied taxes for the item
     *
     * @param \Magento\Tax\Service\V1\Data\OrderTaxDetails\AppliedTax[] $appliedTaxes
     * @return $this
     */
    public function setAppliedTaxes($appliedTaxes)
    {
        $this->_set(Item::KEY_APPLIED_TAXES, $appliedTaxes);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (isset($data[Item::KEY_APPLIED_TAXES])) {
            $appliedTaxDataObjects = [];
            $appliedTaxes = $data[Item::KEY_APPLIED_TAXES];
            foreach ($appliedTaxes as $appliedTax) {
                $appliedTaxDataObjects[] = $this->appliedTaxBuilder->populateWithArray($appliedTax)->create();
            }
            $data[Item::KEY_APPLIED_TAXES] = $appliedTaxDataObjects;
        }

        return parent::_setDataValues($data);
    }
}
