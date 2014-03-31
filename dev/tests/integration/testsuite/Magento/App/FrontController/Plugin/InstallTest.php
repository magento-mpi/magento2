<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\FrontController\Plugin;

class InstallTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Module\Setup
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Module\Setup',
            array('resourceName' => 'default_setup', 'moduleName' => 'Magento_Core')
        );
    }

    public function testApplyAllDataUpdates()
    {
        /*reset versions*/
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Resource\Resource'
        )->setDbVersion(
            'adminnotification_setup',
            false
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Resource\Resource'
        )->setDataVersion(
            'adminnotification_setup',
            false
        );
        $this->_model->deleteTableRow('core_resource', 'code', 'adminnotification_setup');
        $this->_model->getConnection()->dropTable($this->_model->getTable('adminnotification_inbox'));
        $this->_model->getConnection()->dropTable($this->_model->getTable('admin_system_messages'));
        /** @var \Magento\Cache\FrontendInterface $cache */
        $cache = $this->_objectManager->get('Magento\App\Cache\Type\Config');
        $cache->clean();

        try {
            /* This triggers plugin to be executed */
            $this->dispatch('index/index');
        } catch (\Exception $e) {
            $this->fail("Impossible to continue other tests, because database is broken: {$e}");
        }

        try {
            $tableData = $this->_model->getConnection()->describeTable(
                $this->_model->getTable('adminnotification_inbox')
            );
            $this->assertNotEmpty($tableData);
        } catch (\Exception $e) {
            $this->fail("Impossible to continue other tests, because database is broken: {$e}");
        }

        $this->assertNotEmpty(
            $this->_model->getTableRow('core_resource', 'code', 'adminnotification_setup', 'version')
        );
        $this->assertNotEmpty(
            $this->_model->getTableRow('core_resource', 'code', 'adminnotification_setup', 'data_version')
        );
    }
}
