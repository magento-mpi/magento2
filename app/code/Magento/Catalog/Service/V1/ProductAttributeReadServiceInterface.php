<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

/**
 * Class ProductAttributeReadServiceInterface
 * @package Magento\Catalog\Service\V1
 */
interface ProductAttributeReadServiceInterface
{
    /**
     * Retrieve list of product attribute types
     *
     * @return array
     */
    public function getTypes();
}
