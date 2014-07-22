<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option\Type;


interface ReadServiceInterface
{
    /**
     * @return \Magento\Bundle\Service\V1\Data\Option\Type\Metadata[]
     */
    public function getTypes();
}
