<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Backend\Admin;

/**
 * @magentoAppArea adminhtml
 */
class RobotsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Backend\Admin\Robots
     */
    protected $_model = null;

    /**
     * Initialize model
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Config\Backend\Admin\Robots');
        $this->_model->setPath('design/search_engine_robots/custom_instructions');
        $this->_model->afterLoad();
    }

    /**
     * Check that default value is empty when robots.txt not exists
     *
     * @magentoDataFixture Magento/Backend/Model/_files/no_robots_txt.php
     */
    public function testAfterLoadRobotsTxtNotExists()
    {
        $this->assertEmpty($this->_model->getValue());
    }

    /**
     * Check that default value equals to robots.txt content when it is available
     *
     * @magentoDataFixture Magento/Backend/Model/_files/robots_txt.php
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
        $robotsTxtPath = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\App\Dir')->getDir() . DS . 'robots.txt';
        $this->assertFileNotExists($robotsTxtPath, 'robots.txt exists');

        $this->_modifyConfig();
    }

    /**
     * Check robots.txt file changed when robots.txt exists
     *
     * @magentoDataFixture Magento/Backend/Model/_files/robots_txt.php
     * @magentoDbIsolation enabled
     */
    public function testAfterSaveFileExists()
    {
        $robotsTxtPath = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Dir')
                ->getDir() . DS . 'robots.txt';
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
        $this->assertStringEqualsFile(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\App\Dir')->getDir() . DS . 'robots.txt',
            $robotsTxt
        );
    }

    /**
     * Remove created robots.txt
     */
    protected function tearDown()
    {
        require 'Magento/Backend/Model/_files/no_robots_txt.php';
    }
}
