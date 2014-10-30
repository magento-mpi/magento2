<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute;

use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
/**
 * Emtity attribute option model
 *
 * @method \Magento\Eav\Model\Resource\Entity\Attribute\Option _getResource()
 * @method \Magento\Eav\Model\Resource\Entity\Attribute\Option getResource()
 * @method int getAttributeId()
 * @method \Magento\Eav\Model\Entity\Attribute\Option setAttributeId(int $value)
 * @method \Magento\Eav\Model\Entity\Attribute\Option setSortOrder(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Option extends AbstractExtensibleModel implements AttributeOptionInterface
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magento\Eav\Model\Resource\Entity\Attribute\Option');
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData(AttributeOptionInterface::LABEL);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getData(AttributeOptionInterface::VALUE);
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(AttributeOptionInterface::SORT_ORDER);
    }

    /**
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->getData(AttributeOptionInterface::IS_DEFAULT);
    }

    /**
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option\Label[]
     */
    public function getStoreLabels()
    {
        return $this->getData(AttributeOptionInterface::STORE_LABELS);
    }
}
