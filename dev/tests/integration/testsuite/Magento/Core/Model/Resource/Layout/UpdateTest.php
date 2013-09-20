<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Layout;

class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Layout\Update
     */
    protected $_resourceModel;

    protected function setUp()
    {
        $this->_resourceModel = \Mage::getModel('Magento\Core\Model\Resource\Layout\Update');
    }

    /**
     * @magentoDataFixture Magento/Core/_files/layout_update.php
     */
    public function testFetchUpdatesByHandle()
    {
        /** @var $theme \Magento\Core\Model\Theme */
        $theme = \Mage::getModel('Magento\Core\Model\Theme');
        $theme->load('Test Theme', 'theme_title');
        $result = $this->_resourceModel->fetchUpdatesByHandle('test_handle', $theme, \Mage::app()->getStore());
        $this->assertEquals('not_temporary', $result);
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_enabled.php
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Core/_files/layout_cache.php
     */
    public function testSaveAfterClearCache()
    {
        /** @var $appCache \Magento\Core\Model\Cache */
        $appCache = \Mage::getSingleton('Magento\Core\Model\Cache');
        /** @var \Magento\Core\Model\Cache\Type\Layout $layoutCache */
        $layoutCache = \Mage::getSingleton('Magento\Core\Model\Cache\Type\Layout');

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'));
        $this->assertNotEmpty($layoutCache->load('LAYOUT_CACHE_FIXTURE'));

        /** @var $layoutUpdate \Magento\Core\Model\Layout\Update */
        $layoutUpdate = \Mage::getModel('Magento\Core\Model\Layout\Update');
        $this->_resourceModel->save($layoutUpdate);

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'), 'Non-layout cache must be kept');
        $this->assertFalse($layoutCache->load('LAYOUT_CACHE_FIXTURE'), 'Layout cache must be erased');
    }
}
