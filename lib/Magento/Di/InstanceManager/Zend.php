<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Zend\Di\InstanceManager;

class Magento_Di_InstanceManager_Zend extends InstanceManager implements Magento_Di_InstanceManager
{
    /**
     * @var Magento_Di_Generator
     */
    protected $_generator;

    /**
     * @param Magento_Di_Generator $classGenerator
     */
    public function __construct(Magento_Di_Generator $classGenerator = null)
    {
        $this->_generator = $classGenerator ?: new Magento_Di_Generator();
    }

    /**
     * Remove shared instance
     *
     * @param string $classOrAlias
     * @return Magento_Di_InstanceManager_Zend
     */
    public function removeSharedInstance($classOrAlias)
    {
        unset($this->sharedInstances[$classOrAlias]);

        return $this;
    }

    /**
     * Add type preference from configuration
     *
     * @param string $interfaceOrAbstract
     * @param string $implementation
     * @return Zend\Di\InstanceManager
     */
    public function addTypePreference($interfaceOrAbstract, $implementation)
    {
        $this->_generator->generateClass($implementation);
        return parent::addTypePreference($interfaceOrAbstract, $implementation);
    }

    /**
     * Set parameters from configuration
     *
     * @param string $aliasOrClass
     * @param array $parameters
     */
    public function setParameters($aliasOrClass, array $parameters)
    {
        foreach ($parameters as $parameter) {
            if (is_string($parameter)) {
                $this->_generator->generateClass($parameter);
            }
        }
        parent::setParameters($aliasOrClass, $parameters);
    }
}
