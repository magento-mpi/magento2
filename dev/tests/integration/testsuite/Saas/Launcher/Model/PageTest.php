<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Saas/Launcher/_files/pages.php
 * @magentoDataFixture Saas/Launcher/_files/config_bootstrap.php
 */
class Saas_Launcher_Model_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Model_Page
     */
    protected $_page;

    protected function setUp()
    {
        $this->_page = Mage::getModel('Saas_Launcher_Model_Page');
    }

    public function testLoadByCode()
    {
        $this->_page->loadByPageCode('landing_page_1');
        $this->assertEquals('landing_page_1', $this->_page->getPageCode());
    }

    public function testLoadByCodeDoesNotInjectTileCollectionIntoUnknownPage()
    {
        // landing_page_100 has not been defined by fixture
        $this->_page->loadByPageCode('landing_page_100');
        $this->assertNull($this->_page->getTiles());
    }

    public function testGetTiles()
    {
        $this->assertNull($this->_page->getTiles());
        $this->_page->loadByPageCode('landing_page_1');
        $this->assertInstanceOf('Saas_Launcher_Model_Resource_Tile_Collection', $this->_page->getTiles());
        // 2 tiles were provided by fixture
        $this->assertEquals(2, $this->_page->getTiles()->getSize());
    }

    public function testGetTilesReturnsTilesSortedBySortOrder()
    {
        $this->_page->loadByPageCode('landing_page_1');
        // tile_2 is defined with lower sort order (see fixture declaration)
        $this->assertEquals('tile_2', $this->_page->getTiles()->getFirstItem()->getTileCode());
    }

    /**
     * @expectedException Zend_Db_Statement_Exception
     */
    public function testSaveCannotPersistTwoPagesWithTheSameCode()
    {
        // page landing_page_1 has been already created by fixture
        $page = Mage::getModel('Saas_Launcher_Model_Page');
        $page->setPageCode('landing_page_1')
            ->save();
    }
}
