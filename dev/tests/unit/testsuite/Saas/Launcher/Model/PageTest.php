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

class Saas_Launcher_Model_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Model_Page
     */
    protected $_page;

    protected function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_page = $objectManager->getObject('Saas_Launcher_Model_Page');
    }

    protected function tearDown()
    {
        $this->_page = null;
    }

    /**
     * @param boolean $isComplete expected result
     * @param array $tileCompleteFlags states of tiles associated with the tested page
     * @dataProvider isCompleteDataProvider
     * @covers Saas_Launcher_Model_Page::isComplete
     */
    public function testIsComplete($isComplete, $tileCompleteFlags)
    {
        $this->_page->setTiles($this->_getMockedTileCollection($tileCompleteFlags));
        $this->assertEquals($isComplete, $this->_page->isComplete());
    }

    /**
     * @return array
     */
    public function isCompleteDataProvider()
    {
        return array(
            array(
                true, array(true, true, true, true)
            ),
            array(
                false, array(true, false, true, false)
            ),
        );
    }

    /**
     * Retrieve mocked tile collection
     *
     * @param array $tileCompleteFlags states of tiles associated with the tested page
     * @return Saas_Launcher_Model_Resource_Tile_Collection|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockedTileCollection($tileCompleteFlags)
    {
        // Mock tiles
        $tiles = array();
        foreach ($tileCompleteFlags as $isTileComplete) {
            $tile = $this->getMock(
                'Saas_Launcher_Model_Tile',
                array('isComplete'),
                array(),
                '',
                false
            );
            $tile->expects($this->any())
                ->method('isComplete')
                ->will($this->returnValue($isTileComplete));

            $tiles[] = $tile;
        }

        // Mock tile collection
        $tileCollection = $this->getMock(
            'Saas_Launcher_Model_Resource_Tile_Collection',
            array('load', 'getItems', 'getIterator'),
            array(),
            '',
            false
        );
        $tileCollection->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($tiles)));

        return $tileCollection;
    }
}
