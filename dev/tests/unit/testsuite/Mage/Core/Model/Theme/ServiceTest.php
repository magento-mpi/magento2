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

        $themeService = new Mage_Core_Model_Theme_Service($themeMock,
            $this->getMock('Mage_Core_Model_App', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false)
        );
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
     * @covers Mage_Core_Model_Theme_Service::getAssignedThemes
     * @covers Mage_Core_Model_Theme_Service::getUnassignedThemes
     * @dataProvider getAssignedUnassignedThemesDataProvider
     */
    public function testGetAssignedUnassignedThemes($stores, $themes, $expAssignedThemes, $expUnassignedThemes)
    {
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);

        /* Mock list existing stores */
        $appMock = $this->getMock('Mage_Core_Model_App', array('getStores'), array(), '', false);
        $appMock->expects($this->once())
            ->method('getStores')
            ->will($this->returnValue(array_keys($stores)));

        /* Mock assigned themeId to each store */
        $storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $index = 0;
        foreach ($stores as $store => $thisId) {
            $storeConfigMock->expects($this->at($index++))
                ->method('getConfig')
                ->with(Mage_Core_Model_Design_Package::XML_PATH_THEME_ID, $store)
                ->will($this->returnValue($thisId));
        }

        /* Mock list customized themes */
        $themesMock = array();
        foreach ($themes as $themeId) {
            $theme = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
            $theme->expects($this->any())->method('getId')->will($this->returnValue($themeId));
            $themesMock[] = $theme;
        }


        $themeService = $this->getMock('Mage_Core_Model_Theme_Service', array(
            '_getCustomizedFrontThemes'
        ), array($themeMock, $appMock, $storeConfigMock));
        $themeService->expects($this->once())
            ->method('_getCustomizedFrontThemes')
            ->will($this->returnValue($themesMock));

        $assignedThemes = $themeService->getAssignedThemes();
        $unassignedThemes = $themeService->getUnassignedThemes();

        $assignedData = array();
        foreach ($assignedThemes as $theme) {
            $assignedData[$theme->getId()] = $theme->getAssignedStores();
        }
        $this->assertEquals($expAssignedThemes, $assignedData);


        $unassignedData = array();
        foreach ($unassignedThemes as $theme) {
            $unassignedData[] = $theme->getId();
        }
        $this->assertEquals($expUnassignedThemes, $unassignedData);
    }

    /**
     * @return array()
     */
    public function getAssignedUnassignedThemesDataProvider()
    {
        return array(
            array(
                'stores' => array(
                    'store_1' => 1,
                    'store_2' => 4,
                    'store_3' => 3,
                    'store_4' => 8,
                    'store_5' => 3,
                    'store_6' => 10,
                ),
                'themes' => array(1, 2, 3, 4, 5, 6, 7, 8, 9),
                'expectedAssignedThemes' => array(
                    1 => array('store_1'),
                    3 => array('store_3', 'store_5'),
                    4 => array('store_2'),
                    8 => array('store_4'),
                ),
                'expectedUnassignedThemes' => array(2, 5, 6, 7, 9)
            )
        );
    }
}
