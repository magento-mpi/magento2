<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\CustomOptions;

interface ReadServiceInterface
{
    /**
     * Get custom option types
     *
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionType[]
     */
    public function getTypes();
}
