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

use Magento\Catalog\Api\Data\ProductTypeInterface;

class ProductType extends \Magento\Framework\Api\AbstractExtensibleObject implements ProductTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get('name');
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->_get('label');
    }
}
