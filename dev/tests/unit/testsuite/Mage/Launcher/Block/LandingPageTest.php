<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_LandingPage
 */
class Mage_Launcher_Block_LandingPageTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Launcher_Block_LandingPage */
    protected $block;


    protected function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);

        $arguments = array(
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
        );
        $this->block = $objectManager->getBlock('Mage_Launcher_Block_LandingPage', $arguments);
    }

    protected function tearDown()
    {
        $this->block = null;
        $this->tile = null;
    }

    /**
     * @covers Mage_Launcher_Block_Tile::getTileCode
     */
    public function testGetTiles()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $tile = $objectManager->getModel('Mage_Launcher_Model_Tile');
        $tileCode = 'tax';
        $tile->setCode($tileCode);

        $this->assertEquals($tileCode, $this->block->getTiles());
    }
}
