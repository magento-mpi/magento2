<?php

namespace Magento\Catalog\Service\V1\Data;

class Product extends \Magento\Service\Data\EAV\AbstractObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const ID = 'id';

    const SKU = 'sku';

    const PRICE = 'price';

    const TAX_CLASS_ID = 'tax_class_id';

    const QTY = 'qty';

    const IS_IN_STOCK = 'is_in_stock';

    const WEIGHT = 'weight';

    const CATEGORY_IDS = 'category_ids';

    const DESCRIPTION = 'description';

    const STATUS = 'status';

    const SHORT_DESCRIPTION = 'short_description';

    const VISIBILITY = 'visibility';

    const TYPE_ID = 'type_id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const STORE_ID = 'store_id';
    /**#@-*/

    public function getId()
    {
        return $this->_get(self::ID);
    }

    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    public function getShortDescription()
    {
        return $this->_get(self::SHORT_DESCRIPTION);
    }

    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    public function getVisibility()
    {
        return $this->_get(self::VISIBILITY);
    }

    public function getCategoryIds()
    {
        return $this->_get(self::CATEGORY_IDS);
    }

    public function getTypeId()
    {
        return $this->_get(self::TYPE_ID);
    }

    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }
}
