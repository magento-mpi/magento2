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
 * Magento filter factory abstract
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * Set of filters
     *
     * @var array
     */
    protected $invokableClasses = array();

    /**
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $shareByDefault = true;

    /**
     * Shared instances, by default is shared
     *
     * @var array
     */
    protected $shared = array();

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Zend_Filter_Interface[]
     */
    protected $sharedInstances = array();

    /**
     * @param \Magento\ObjectManager $objectManger
     */
    public function __construct(\Magento\ObjectManager $objectManger)
    {
        $this->objectManager = $objectManger;
    }

    /**
     * Check is it possible to create a filter by given name
     *
     * @param string $alias
     * @return bool
     */
    public function canCreateFilter($alias)
    {
        return array_key_exists($alias, $this->invokableClasses);
    }

    /**
     * Check is shared filter
     *
     * @param string $class
     * @return bool
     */
    public function isShared($class)
    {
        return isset($this->shared[$class]) ? $this->shared[$class] : $this->shareByDefault;
    }

    /**
     * Create a filter by given name
     *
     * @param string $alias
     * @param array $arguments
     * @return \Zend_Filter_Interface
     */
    public function createFilter($alias, array $arguments = array())
    {
        $addToShared = !$arguments || isset($this->sharedInstances[$alias])
            xor $this->isShared($this->invokableClasses[$alias]);

        if (!isset($this->sharedInstances[$alias])) {
            $filter = $this->objectManager->create($this->invokableClasses[$alias], $arguments);
        } else {
            $filter = $this->sharedInstances[$alias];
        }

        if ($addToShared) {
            $this->sharedInstances[$alias] = $filter;
        }

        return $filter;
    }
}
