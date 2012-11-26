<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Launcher/_files/pages.php
 * @magentoDataFixture Mage/Launcher/_files/config_bootstrap.php
 */
class Mage_Launcher_Model_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Page
     */
    protected $_page;

    protected function setUp()
    {
        $this->_page = Mage::getModel('Mage_Launcher_Model_Page');
    }

    public function testLoadByCode()
    {
        $this->_page->loadByCode('landing_page_1');
        $this->assertEquals('landing_page_1', $this->_page->getCode());
    }

    public function testLoadByCodeDoesNotInjectTileCollectionIntoUnknownPage()
    {
        // landing_page_100 has not been defined by fixture
        $this->_page->loadByCode('landing_page_100');
        $this->assertNull($this->_page->getTiles());
    }

    public function testGetTiles()
    {
        $this->assertNull($this->_page->getTiles());
        $this->_page->loadByCode('landing_page_1');
        $this->assertInstanceOf('Mage_Launcher_Model_Resource_Tile_Collection', $this->_page->getTiles());
        // 2 tiles were provided by fixture
        $this->assertEquals(2, $this->_page->getTiles()->getSize());
    }

    public function testGetTilesReturnsTilesSortedBySortOrder()
    {
        $this->_page->loadByCode('landing_page_1');
        // tile_2 is defined with lower sort order (see fixture declaration)
        $this->assertEquals('tile_2', $this->_page->getTiles()->getFirstItem()->getCode());
    }

    /**
     * @expectedException Zend_Db_Statement_Exception
     */
    public function testSaveCannotPersistTwoPagesWithTheSameCode()
    {
        // page landing_page_1 has been already created by fixture
        $page = Mage::getModel('Mage_Launcher_Model_Page');
        $page->setCode('landing_page_1')
            ->save();
    }
}
