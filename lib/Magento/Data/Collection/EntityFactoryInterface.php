<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Collection;

interface EntityFactoryInterface
{

    /**
     * Create new object instance
     *
     * @param string $type
     * @param array $arguments
     * @return mixed
     */
    public function create($type, array $arguments = array());
}
