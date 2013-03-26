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
 * Test theme virtual model
 */
class Mage_Core_Model_Theme_Domain_VirtualTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get existing staging theme
     *
     * @covers Mage_Core_Model_Theme_Domain_Virtual::getStagingTheme
     */
    public function testGetStagingThemeExisting()
    {
        $themeId = 15;
        $stageThemeId = 20;

        $stageThemeMock = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
        $stageThemeMock->expects($this->atLeastOnce())->method('getId')->will($this->returnValue($stageThemeId));

        $themeCollection = $this->getMock(
            'Mage_Core_Model_Resource_Theme_Collection',
            array('addFieldToFilter', 'getFirstItem'),
            array(),
            '',
            false
        );
        $themeCollection->expects($this->at(0))
            ->method('addFieldToFilter')
            ->with('parent_id', $themeId)
            ->will($this->returnSelf());
        $themeCollection->expects($this->at(1))
            ->method('addFieldToFilter')
            ->with('type', Mage_Core_Model_Theme::TYPE_STAGING)
            ->will($this->returnSelf());
        $themeCollection->expects($this->atLeastOnce())
            ->method('getFirstItem')
            ->will($this->returnValue($stageThemeMock));

        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('getCollection', 'getId'), array(), '', false);
        $themeMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($themeCollection));
        $themeMock->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue($themeId));


        $copVtS = $this->getMock('Mage_Core_Model_Theme_Copy_VirtualToStaging', array(), array(), '', false);

        $virtualTheme = $this->getMock(
            'Mage_Core_Model_Theme_Domain_Virtual',
            array('_createStagingTheme'),
            array('theme' => $themeMock, 'copyModelVS' => $copVtS)
        );
        $virtualTheme->expects($this->never())->method('_createStagingTheme');

        $this->assertEquals($stageThemeMock, $virtualTheme->getStagingTheme());
        $this->assertEquals($stageThemeMock, $virtualTheme->getStagingTheme());
    }

    /**
     * Test creating staging theme
     *
     * @covers Mage_Core_Model_Theme_Domain_Virtual::getStagingTheme
     */
    public function testGetStagingThemeNew()
    {
        $emptyStageMock = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
        $emptyStageMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $newStageMock = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);

        $themeMock = $this->getMock(
            'Mage_Core_Model_Theme', array('getCollection', 'getId'), array(), '', false, false
        );

        $copVtS = $this->getMock('Mage_Core_Model_Theme_Copy_VirtualToStaging', array('copy'), array(), '', false);
        $copVtS->expects($this->once())
            ->method('copy')
            ->with($themeMock)
            ->will($this->returnValue($newStageMock));

        $virtualTheme = $this->getMock(
            'Mage_Core_Model_Theme_Domain_Virtual',
            array('_getStagingTheme'),
            array('theme' => $themeMock, 'copyModelVS' => $copVtS)
        );
        $virtualTheme->expects($this->once())
            ->method('_getStagingTheme')
            ->will($this->returnValue($emptyStageMock));

        $this->assertEquals($newStageMock, $virtualTheme->getStagingTheme());
        $this->assertEquals($newStageMock, $virtualTheme->getStagingTheme());
    }
}
