<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

/**
 * Magento filter factory interface
 */
interface FactoryInterface
{
    /**
     * Check is it possible to create a filter by given name
     *
     * @param string $alias
     * @return bool
     */
    public function canCreateFilter($alias);

    /**
     * Check is shared filter
     *
     * @param string $class
     * @return bool
     */
    public function isShared($class);

    /**
     * Create a filter by given name
     *
     * @param string $alias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     */
    public function createFilter($alias, array $arguments = array());
}
