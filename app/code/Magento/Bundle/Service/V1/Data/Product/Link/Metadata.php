<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product\Link;

use \Magento\Framework\Service\Data\AbstractObject;

/**
 * @codeCoverageIgnore
 */
class Metadata extends AbstractObject
{
    const SKU = 'sku';

    const OPTION_ID = 'option_id';

    const QTY = 'qty';

    const POSITION = 'position';

    const DEFINED = 'defined';

    const IS_DEFAULT = 'is_default';

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * @return int|null
     */
    public function getOptionId()
    {
        return $this->_get(self::OPTION_ID);
    }

    /**
     * @return float|null
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * @return bool
     */
    public function isDefined()
    {
        return (bool)$this->_get(self::DEFINED);
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return (bool)$this->_get(self::IS_DEFAULT);
    }
}
