<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme service model
 */
class Mage_Core_Model_Theme_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Core_Model_Theme_Service::isPresentCustomizedThemes
     * @dataProvider isPresentCustomizedThemesDataProvider
     */
    public function testIsPresentCustomizedThemes($availableIsVirtual, $expectedResult)
    {
        $themeCollectionMock = array();
        foreach ($availableIsVirtual as $isVirtual) {
            /** @var $themeItemMock Mage_Core_Model_Theme */
            $themeItemMock = $this->getMock('Mage_Core_Model_Theme', array('isVirtual'), array(), '', false);
            $themeItemMock->expects($this->any())
                ->method('isVirtual')
                ->will($this->returnValue($isVirtual));
            $themeCollectionMock[] = $themeItemMock;
        }

        /** @var $themeMock Mage_Core_Model_Theme */
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('getCollection'), array(), '', false);
        $themeMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($themeCollectionMock));

        $designMock = $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false);
        $configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $themeService = new Mage_Core_Model_Theme_Service($themeMock, $designMock, $configMock);
        $this->assertEquals($expectedResult, $themeService->isPresentCustomizedThemes());
    }

    /**
     * @return array
     */
    public function isPresentCustomizedThemesDataProvider()
    {
        return array(
            array(array(false, false, false), false),
            array(array(false, true, false), true)
        );
    }

    /**
     * @covers Mage_Core_Model_Theme_Service::isPresentCustomizedThemes
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Theme doesn't recognized. Requested id: -1
     */
    public function testAssignThemeToStoresWrongThemeId()
    {
        /** @var $themeMock Mage_Core_Model_Theme */
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('load', 'getId'), array(), '', false);
        $themeMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(-1))
            ->will($this->returnValue($themeMock));

        $themeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(false));

        $designMock = $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false);
        $configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $themeService = new Mage_Core_Model_Theme_Service($themeMock, $designMock, $configMock);
        $themeService->assignThemeToStores(-1, array());
    }

    /**
     * @covers Mage_Core_Model_Theme_Service::isPresentCustomizedThemes
     * @dataProvider assignThemeToStoresDataProvider
     */
    public function testAssignThemeToStores($themeId, $stores, $scope, $area)
    {
        /** @var $themeMock Mage_Core_Model_Theme */
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('load', 'getId'), array(), '', false);
        $themeMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo($themeId))
            ->will($this->returnValue($themeMock));

        $themeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($themeId));

        $designMock = $this->getMock(
            'Mage_Core_Model_Design_Package', array('getConfigPathByArea'), array(), '', false
        );
        $designMock->expects($this->any())
            ->method('getConfigPathByArea')
            ->with($this->equalTo($area))
            ->will($this->returnValue($area));

        $configMock = $this->getMock('Mage_Core_Model_Config', array('saveConfig'), array(), '', false);
        $configMock->expects($this->any())
            ->method('saveConfig')
            ->with($this->equalTo($area), $this->equalTo($themeId), $this->equalTo($scope), $this->anything());

        Mage_Core_Model_Design_Package::DEFAULT_AREA;
        $themeService = new Mage_Core_Model_Theme_Service($themeMock, $designMock, $configMock);
        $this->assertInstanceOf('Mage_Core_Model_Theme_Service',
            $themeService->assignThemeToStores($themeId, $stores, $scope, $area));
    }

    /**
     * @return array
     */
    public function assignThemeToStoresDataProvider()
    {
        return array(
            array(1, array(1,2,3,4,5), 'stores', 'frontend'),
            array(2, array(1,2,3), 'websites', 'frontend'),
            array(3, array(1,2), 'default', 'adminhtml'),
            array(4, array(), 'stores', 'adminhtml'),
            array(5, array(1,2,3,4), 'stores', 'api'),
        );
    }
}
