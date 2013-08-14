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
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve tax tile block instance
     *
     * @param int $taxRuleCount
     * @return Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile
     */
    protected function _getTileBlockInstance($taxRuleCount)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        // mock tax rule collection instance
        $taxRuleCollection = $this->getMock(
            'Magento_Tax_Model_Resource_Calculation_Rule_Collection', array('getSize'), array(), '', false
        );
        $taxRuleCollection->expects($this->any())->method('getSize')->will($this->returnValue($taxRuleCount));

        $tileBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile',
            array(
                'taxRuleCollection' => $taxRuleCollection,
            )
        );

        return $tileBlock;
    }

    public function testGetTaxRuleCount()
    {
        $taxRuleCount = 100;
        $tileBlock = $this->_getTileBlockInstance($taxRuleCount);
        $this->assertEquals($taxRuleCount, $tileBlock->getTaxRuleCount());
    }
}
