<?php
/**
 * Test object manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework;

class ObjectManager extends \Magento\Framework\App\ObjectManager
{
    /**
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = array('Magento\Framework\View\Layout', 'Magento\Registry');

    /**
     * @var array
     */
    protected $persistedInstances = array(
        'Magento\Framework\App\Resource',
        'Magento\Framework\Config\Scope',
        'Magento\Framework\ObjectManager\Relations',
        'Magento\Framework\ObjectManager\Config',
        'Magento\Interception\Definition',
        'Magento\Framework\ObjectManager\Definition',
        'Magento\Session\Config',
        'Magento\Framework\ObjectManager\Config\Mapper\Dom'
    );

    /**
     * Clear InstanceManager cache
     *
     * @return \Magento\TestFramework\ObjectManager
     */
    public function clearCache()
    {
        foreach ($this->_classesToDestruct as $className) {
            if (isset($this->_sharedInstances[$className])) {
                $this->_sharedInstances[$className]->__destruct();
            }
        }

        \Magento\Framework\App\Config\Base::destroy();
        $sharedInstances = array('Magento\Framework\ObjectManager' => $this, 'Magento\Framework\App\ObjectManager' => $this);
        foreach ($this->persistedInstances as $persistedClass) {
            if (isset($this->_sharedInstances[$persistedClass])) {
                $sharedInstances[$persistedClass] = $this->_sharedInstances[$persistedClass];
            }
        }
        $this->_sharedInstances = $sharedInstances;
        $this->_config->clean();

        return $this;
    }

    /**
     * Add shared instance
     *
     * @param mixed $instance
     * @param string $className
     */
    public function addSharedInstance($instance, $className)
    {
        $this->_sharedInstances[$className] = $instance;
    }

    /**
     * Remove shared instance
     *
     * @param string $className
     */
    public function removeSharedInstance($className)
    {
        unset($this->_sharedInstances[$className]);
    }

    /**
     * Set objectManager
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @return \Magento\Framework\ObjectManager
     */
    public static function setInstance(\Magento\Framework\ObjectManager $objectManager)
    {
        return self::$_instance = $objectManager;
    }

    /**
     * @return \Magento\Framework\ObjectManager\Factory|\Magento\Framework\ObjectManager\Factory\Factory
     */
    public function getFactory()
    {
        return $this->_factory;
    }
}
