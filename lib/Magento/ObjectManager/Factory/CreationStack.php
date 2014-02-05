<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager\Factory;

/**
 * Tracking of classes being recursively created
 */
class CreationStack
{
    /**
     * @var array
     */
    private $classes = array();

    /**
     * Append class to the end of the stack
     *
     * @param string $class
     * @throws \LogicException
     */
    public function add($class)
    {
        if (isset($this->classes[$class])) {
            reset($this->classes);
            $rootClass = key($this->classes);
            $this->classes = array();
            throw new \LogicException("Circular dependency: $class depends on $rootClass and vice versa.");
        }
        $this->classes[$class] = 1;
    }

    /**
     * Removed class from the stack, regardless of its position
     *
     * @param string $class
     */
    public function remove($class)
    {
        unset($this->classes[$class]);
    }
}
