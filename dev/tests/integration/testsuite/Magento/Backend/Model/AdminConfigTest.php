<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

/**
 * Test class for \Magento\Backend\Model\AdminConfig.
 *
 * @magentoAppArea adminhtml
 */
class AdminConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\AdminConfig
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\AdminConfig');
    }

    public function testConstructor()
    {
       $this->assertEquals('/backend', $this->_model->getCookiePath());
    }
}
