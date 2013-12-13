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

class ObjectManager extends \Magento\App\ObjectManager
{
    /**
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = array(
        'Magento\Core\Model\Layout',
        'Magento\Core\Model\Registry'
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

        \Magento\Core\Model\Config\Base::destroy();
        $sharedInstances = array(
            'Magento\ObjectManager' => $this, 'Magento\App\ObjectManager' => $this
        );
        if (isset($this->_sharedInstances['Magento\App\Resource'])) {
            $sharedInstances['Magento\App\Resource'] = $this->_sharedInstances['Magento\App\Resource'];
        }

        if (isset($this->_sharedInstances['Magento\Config\Scope'])) {
            $sharedInstances['Magento\Config\Scope'] = $this->_sharedInstances['Magento\Config\Scope'];
        }

        if (isset($this->_sharedInstances['Magento\Core\Model\Session\Config'])) {
            $sharedInstances['Magento\Core\Model\Session\Config'] =
                $this->_sharedInstances['Magento\Core\Model\Session\Config'];
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
     * @param \Magento\ObjectManager $objectManager
     * @return \Magento\ObjectManager
     */
    public static function setInstance(\Magento\ObjectManager $objectManager)
    {
        return self::$_instance = $objectManager;
    }

    /**
     * @return \Magento\ObjectManager\Factory|\Magento\ObjectManager\Factory\Factory
     */
    public function getFactory()
    {
        return $this->_factory;
    }
}
