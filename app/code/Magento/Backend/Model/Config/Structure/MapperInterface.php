<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Converter Mapper Interface
 */
namespace Magento\Backend\Model\Config\Structure;

interface MapperInterface
{
    /**
     * Apply map
     *
     * @param array $data
     * @return array
     */
    public function map(array $data);
}
