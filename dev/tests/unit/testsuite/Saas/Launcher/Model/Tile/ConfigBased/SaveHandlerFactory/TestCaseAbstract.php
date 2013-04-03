<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerFactory_TestCaseAbstract
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerFactoryAbstract
     */
    protected $_saveHandlerFactory;

    protected function setUp()
    {
        // Mock object manager
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_saveHandlerFactory = $this->getSaveHandlerFactoryInstance($objectManager);
    }

    protected function tearDown()
    {
        $this->_saveHandlerFactory = null;
    }

    /**
     * @param Magento_ObjectManager $objectManager
     * @return Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerFactoryAbstract
     */
    abstract public function getSaveHandlerFactoryInstance(Magento_ObjectManager $objectManager);


    public function testGetSaveHandlerMapContainsValidSaveHandlers()
    {
        foreach ($this->_saveHandlerFactory->getSaveHandlerMap() as $saveHandlerClassName) {
            $this->assertTrue(class_exists($saveHandlerClassName));
            $saveHandlerClass = new ReflectionClass($saveHandlerClassName);
            $this->assertTrue(
                $saveHandlerClass->isSubclassOf('Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract')
            );
        }
    }
}
