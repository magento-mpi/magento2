<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

/**
 * Class Product
 * @package Magento\Catalog\Service\V1\Data
 */
class Product extends \Magento\Framework\Service\Data\Eav\AbstractObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const ID = 'id';

    const SKU = 'sku';

    const NAME = 'name';

    const PRICE = 'price';

    const WEIGHT = 'weight';

    const STATUS = 'status';

    const ATTRIBUTE_SET_ID = 'attribute_set_id';

    const VISIBILITY = 'visibility';

    const TYPE_ID = 'type_id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const STORE_ID = 'store_id';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @return int|null
     */
    public function getAttributeSetId()
    {
        return $this->_get(self::ATTRIBUTE_SET_ID);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * @return int|null
     */
    public function getVisibility()
    {
        return $this->_get(self::VISIBILITY);
    }

    /**
     * @return string|null
     */
    public function getTypeId()
    {
        return $this->_get(self::TYPE_ID);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }
}
