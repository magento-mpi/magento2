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
    private $name;

    /**
     * Product type label
     *
     * @var string
     */
    private $label;


    /**
     * @param string $name
     * @param string $label
     */
    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->label;
    }
}
