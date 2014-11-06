<?php
/**
 * Product type
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

use \Magento\Catalog\Api\Data\ProductTypeInterface;

class ProductType implements ProductTypeInterface
{
    /**
     * Product type name
     *
     * @var string
     */
    private $key;

    /**
     * Product type label
     *
     * @var string
     */
    private $value;


    /**
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}
