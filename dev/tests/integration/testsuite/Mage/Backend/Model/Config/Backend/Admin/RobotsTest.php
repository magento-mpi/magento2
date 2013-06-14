<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Backend_Model_Config_Backend_Admin_RobotsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Backend_Admin_Robots
     */
    protected $_model = null;

    /**
     * Initialize model
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_model = Mage::getModel('Mage_Backend_Model_Config_Backend_Admin_Robots');
        $this->_model->setPath('design/search_engine_robots/custom_instructions');
        $this->_model->afterLoad();
    }

    /**
     * Check that default value is empty when robots.txt not exists
     *
     * @magentoDataFixture Mage/Backend/Model/_files/no_robots_txt.php
     */
    public function testAfterLoadRobotsTxtNotExists()
    {
        $this->assertEmpty($this->_model->getValue());
    }

    /**
     * Check that default value equals to robots.txt content when it is available
     *
     * @magentoDataFixture Mage/Backend/Model/_files/robots_txt.php
     */
    public function testAfterLoadRobotsTxtExists()
    {
        $this->assertEquals('Sitemap: http://store.com/sitemap.xml', $this->_model->getValue());
    }

    /**
     * Check robots.txt file generated when robots.txt not exists
     *
     * @magentoDbIsolation enabled
     */
    public function testAfterSaveFileNotExists()
    {
        $robotsTxtPath = Mage::getBaseDir() . DS . 'robots.txt';
        $this->assertFileNotExists($robotsTxtPath, 'robots.txt exists');

        $this->_modifyConfig();
    }

    /**
     * Check robots.txt file changed when robots.txt exists
     *
     * @magentoDataFixture Mage/Backend/Model/_files/robots_txt.php
     * @magentoDbIsolation enabled
     */
    public function testAfterSaveFileExists()
    {
        $robotsTxtPath = Mage::getBaseDir() . DS . 'robots.txt';
        $this->assertFileExists($robotsTxtPath, 'robots.txt exists');

        $this->_modifyConfig();
    }

    /**
     * Modify config value and check all changes were written into robots.txt
     */
    protected function _modifyConfig()
    {
        $robotsTxt = "User-Agent: *\nDisallow: /checkout";
        $this->_model->setValue($robotsTxt)->save();
        $this->assertStringEqualsFile(Mage::getBaseDir() . DS . 'robots.txt', $robotsTxt);
    }

    /**
     * Remove created robots.txt
     */
    protected function tearDown()
    {
        require 'Mage/Backend/Model/_files/no_robots_txt.php';
    }
}
