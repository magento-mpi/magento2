<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme service model
 */
class Mage_Core_Model_Theme_ServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGetNotCustomizedFrontThemes()
    {
        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Service');
        $collection = $themeService->getPhysicalThemes(1,
            Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE);

        $this->assertLessThanOrEqual(
            Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE, $collection->count()
        );

        /** @var $theme Mage_Core_Model_Theme */
        foreach ($collection as $theme) {
            $this->assertEquals('frontend', $theme->getArea());
            $this->assertFalse($theme->isVirtual());
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @covers Mage_Core_Model_Theme_Service::assignThemeToStores
     */
    public function testAssignThemeToStores($themeId, $stores, $scope, $area)
    {
        /** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
        $themeCollection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
        $originalCount = $themeCollection->count();

        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Service');

//        $themeService->assignThemeToStores();
//        /** @var $themeMock Mage_Core_Model_Theme */
//        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('load', 'getId', 'isVirtual'), array(), '', false);
//        $themeMock->expects($this->once())
//            ->method('load')
//            ->with($this->equalTo($themeId))
//            ->will($this->returnValue($themeMock));
//
//        $themeMock->expects($this->any())
//            ->method('getId')
//            ->will($this->returnValue($themeId));
//
//        $themeMock->expects($this->any())
//            ->method('isVirtual')
//            ->will($this->returnValue($themeMock));
//
//        $designMock = $this->getMock(
//            'Mage_Core_Model_Design_Package', array('getConfigPathByArea'), array(), '', false
//        );
//        $designMock->expects($this->once())
//            ->method('getConfigPathByArea')
//            ->with($this->equalTo($area))
//            ->will($this->returnValue($area));
//
//        $appMock = $this->getMock('Mage_Core_Model_App', array('getConfig', 'getConfigDataModel', 'saveConfig'),
//            array(), '', false);
//        $appMock->expects($this->any())
//            ->method('getConfig')
//            ->will($this->returnValue($appMock));
//
//        $appMock->expects($this->any())
//            ->method('getConfigDataModel')
//            ->will($this->returnValue($appMock));
//
//        $appMock->expects($this->any())
//            ->method('saveConfig')
//            ->with($this->equalTo($area), $this->equalTo($themeId), $this->equalTo($scope), $this->anything());
//
//        $helperMock = $this->getMock('Mage_Core_Helper_Data', array(), array(), '', false);
//        $themeService = new Mage_Core_Model_Theme_Service($themeMock, $designMock, $appMock, $helperMock);
//        $this->assertInstanceOf('Mage_Core_Model_Theme_Service',
//            $themeService->assignThemeToStores($themeId, $stores, $scope, $area));
    }
}
