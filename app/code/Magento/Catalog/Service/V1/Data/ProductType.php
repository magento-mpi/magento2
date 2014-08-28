<?php
/**
 * Product type data object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use \Magento\Framework\Service\Data\AbstractObject;

/**
 * @codeCoverageIgnore
 */
class ProductType extends AbstractObject
{
    const NAME = 'name';
    const LABEL = 'label';

    /**
     * Retrieve product type name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Retrieve product type label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }
}
