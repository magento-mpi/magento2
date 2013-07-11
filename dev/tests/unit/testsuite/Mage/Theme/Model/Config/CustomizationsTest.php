<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme customization config model
 */
class Mage_Theme_Model_Config_CustomizationsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    protected $_designPackage;

    /**
     * @var Mage_Core_Model_Resource_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * @var Mage_Theme_Model_Config_Customizations
     */
    protected $_model;

    protected function setUp()
    {
        $this->_storeManager = $this->getMockForAbstractClass(
            'Mage_Core_Model_StoreManagerInterface', array(), '', true, true, true, array('getStores')
        );
        $this->_designPackage = $this->getMockForAbstractClass(
            'Mage_Core_Model_Design_PackageInterface', array(), '', true, true, true,
            array('getConfigurationDesignTheme')
        );
        $this->_themeCollection = $this->getMock(
            'Mage_Core_Model_Resource_Theme_Collection', array('filterThemeCustomizations', 'load'), array(), '', false
        );

        $collectionFactory = $this->getMock(
            'Mage_Core_Model_Resource_Theme_CollectionFactory', array('create'), array(), '', false
        );

        $collectionFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_themeCollection));

        $itemsProperty = new ReflectionProperty($this->_themeCollection, '_items');
        $itemsProperty->setAccessible(true);
        $itemsProperty->setValue(
            $this->_themeCollection, array($this->_getAssignedTheme(), $this->_getUnassignedTheme())
        );

        $this->_designPackage->expects($this->once())
            ->method('getConfigurationDesignTheme')
            ->will($this->returnValue($this->_getAssignedTheme()->getId()));

        $this->_model = new Mage_Theme_Model_Config_Customizations(
            $this->_storeManager,
            $this->_designPackage,
            $collectionFactory
        );
    }

    protected function tearDown()
    {
        $this->_storeManager    = null;
        $this->_designPackage   = null;
        $this->_themeCollection = null;
        $this->_model           = null;
    }

    /**
     * @covers Mage_Theme_Model_Config_Customizations::getAssignedThemeCustomizations
     */
    public function testGetAssignedThemeCustomizations()
    {
        $this->_themeCollection->expects($this->once())->method('load')->will(
            $this->returnValue(array($this->_getAssignedTheme(), $this->_getUnassignedTheme()))
        );

        $this->_storeManager->expects($this->once())
            ->method('getStores')
            ->will($this->returnValue(array($this->_getStore())));

        $assignedThemes = $this->_model->getAssignedThemeCustomizations();
        $this->assertArrayHasKey($this->_getAssignedTheme()->getId(), $assignedThemes);
    }

    /**
     * @covers Mage_Theme_Model_Config_Customizations::getUnassignedThemeCustomizations
     */
    public function testGetUnassignedThemeCustomizations()
    {
        $this->_themeCollection->expects($this->once())->method('load')->will(
            $this->returnValue(array($this->_getAssignedTheme(), $this->_getUnassignedTheme()))
        );

        $this->_storeManager->expects($this->once())
            ->method('getStores')
            ->will($this->returnValue(array($this->_getStore())));

        $unassignedThemes = $this->_model->getUnassignedThemeCustomizations();
        $this->assertArrayHasKey($this->_getUnassignedTheme()->getId(), $unassignedThemes);
    }

    /**
     * @covers Mage_Theme_Model_Config_Customizations::getStoresByThemes
     */
    public function testGetStoresByThemes()
    {
        $this->_storeManager->expects($this->once())
            ->method('getStores')
            ->will($this->returnValue(array($this->_getStore())));

        $stores = $this->_model->getStoresByThemes();
        $this->assertArrayHasKey($this->_getAssignedTheme()->getId(), $stores);
    }

    /**
     * @covers Mage_Theme_Model_Config_Customizations::isThemeAssignedToStore
     */
    public function testIsThemeAssignedToDefaultStore()
    {
        $this->_themeCollection->expects($this->once())->method('load')->will(
            $this->returnValue(array($this->_getAssignedTheme(), $this->_getUnassignedTheme()))
        );

        $this->_storeManager->expects($this->once())
            ->method('getStores')
            ->will($this->returnValue(array($this->_getStore())));

        $themeAssigned = $this->_model->isThemeAssignedToStore($this->_getAssignedTheme());
        $this->assertEquals(true, $themeAssigned);
    }

    /**
     * @covers Mage_Theme_Model_Config_Customizations::isThemeAssignedToStore
     */
    public function testIsThemeAssignedToConcreteStore()
    {
        $themeUnassigned = $this->_model->isThemeAssignedToStore($this->_getUnassignedTheme(), $this->_getStore());
        $this->assertEquals(false, $themeUnassigned);
    }

    /**
     * @return Varien_Object
     */
    protected function _getAssignedTheme()
    {
        return new Varien_Object(array('id' => 1));
    }

    /**
     * @return Varien_Object
     */
    protected function _getUnassignedTheme()
    {
        return new Varien_Object(array('id' => 2));
    }

    /**
     * @return Varien_Object
     */
    protected function _getStore()
    {
        return new Varien_Object(array('id' => 55));
    }
}
