<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model\Carrier\Source;

/**
 * Class GenericDefault
 * Default implementation of generic carrier source
 *
 * @package Magento\Shipping\Model\Carrier\Source
 */
class GenericDefault implements GenericInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array();
    }
}
