<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product;

use \Magento\Framework\Service\Data\AbstractObject;

/**
 * @codeCoverageIgnore
 */
class Option extends AbstractObject
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
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Get is required option
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_get(self::REQUIRED);
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Get option position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Get product sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }
}
