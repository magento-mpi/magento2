<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product;

use \Magento\Framework\Service\Data\AbstractExtensibleObject;

/**
 * @codeCoverageIgnore
 */
class Option extends AbstractExtensibleObject
{
    const ID = 'id';

    const TITLE = 'title';

    const REQUIRED = 'required';

    const TYPE = 'type';

    const POSITION = 'position';

    const SKU = 'sku';

    /**
     * Get option id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get option title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Get is required option
     *
     * @return bool|null
     */
    public function isRequired()
    {
        return $this->_get(self::REQUIRED);
    }

    /**
     * Get input type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Get option position
     *
     * @return int|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Get product sku
     *
     * @return string|null
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }
}
