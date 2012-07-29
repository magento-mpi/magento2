<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_Factory_FactoryAbstract implements Magento_ObjectManager_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @abstract
     * @param array $arguments
     * @return mixed
     */
    public function createFromArray(array $arguments = array(), $className = null)
    {

    }
}
