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

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Drawer
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve tax drawer block instance
     *
     * @param boolean $isTileComplete
     * @param int $taxRuleCount
     * @return Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Drawer
     */
    protected function _getDrawerBlockInstance($isTileComplete, $taxRuleCount)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        // mock tax rule collection instance
        $taxRuleCollection = $this->getMock(
            'Magento_Tax_Model_Resource_Calculation_Rule_Collection',
            array('getSize'),
            array(),
            '',
            false
        );
        $taxRuleCollection->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue($taxRuleCount));

        $drawerBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Drawer',
            array(
                'taxRuleCollection' => $taxRuleCollection,
            )
        );
        // inject associated tile into drawer block instance
        /** @var $tile PHPUnit_Framework_MockObject_MockObject|Saas_Launcher_Model_Tile */
        $tile = $this->getMock('Saas_Launcher_Model_Tile', array('isComplete'), array(), '', false);
        $tile->expects($this->any())
            ->method('isComplete')
            ->will($this->returnValue($isTileComplete));
        $drawerBlock->setTile($tile);
        return $drawerBlock;
    }

    public function testGetTaxRuleCount()
    {
        $taxRuleCount = 100;
        $drawerBlock = $this->_getDrawerBlockInstance(false, $taxRuleCount);
        $this->assertEquals($taxRuleCount, $drawerBlock->getTaxRuleCount());
    }

    /**
     * @param boolean $expectedResult
     * @param boolean $isTileComplete shows if associated tile is complete
     * @param int $taxRuleCount
     * @dataProvider isUseTaxControlSwitchedOffDataProvider
     */
    public function testIsUseTaxControlSwitchedOff($expectedResult, $isTileComplete, $taxRuleCount)
    {
        $drawerBlock = $this->_getDrawerBlockInstance($isTileComplete, $taxRuleCount);
        $this->assertEquals($expectedResult, $drawerBlock->isUseTaxControlSwitchedOff());
    }

    /**
     * @return array
     */
    public function isUseTaxControlSwitchedOffDataProvider()
    {
        return array(
            array(true, true, 0),
            array(false, true, 100),
            array(false, false, 0),
            array(false, false, 100),
        );
    }

    /**
     * @param boolean $expectedResult
     * @param boolean $isTileComplete shows if associated tile is complete
     * @param int $taxRuleCount
     * @dataProvider isUseTaxControlDisabledDataProvider
     */
    public function testIsUseTaxControlDisabled($expectedResult, $isTileComplete, $taxRuleCount)
    {
        $drawerBlock = $this->_getDrawerBlockInstance($isTileComplete, $taxRuleCount);
        $this->assertEquals($expectedResult, $drawerBlock->isUseTaxControlDisabled());
    }

    /**
     * @return array
     */
    public function isUseTaxControlDisabledDataProvider()
    {
        return array(
            array(false, true, 0),
            array(true, true, 100),
            array(false, false, 0),
            array(false, false, 100),
        );
    }
}
