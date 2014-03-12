<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer;

interface AvailabilityFlagInterface
{
    /**
     * @param $layer
     * @param $filters
     * @return bool
     */
    public function isEnabled($layer, $filters);
} 
