<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module;

class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Module\Setup
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\Module\Setup',
            array('resourceName' => 'default_setup', 'moduleName' => 'Magento_Core')
        );
    }

    public function testSetTable()
    {
        $this->_model->setTable('test_name', 'test_real_name');
        $this->assertEquals('test_real_name', $this->_model->getTable('test_name'));
    }

    public function testApplyAllDataUpdates()
    {
        /*reset versions*/
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            '\Magento\Framework\Module\ResourceInterface'
        )->setDbVersion(
            'adminnotification_setup',
            false
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            '\Magento\Framework\Module\ResourceInterface'
        )->setDataVersion(
            'adminnotification_setup',
            false
        );
        $this->_model->deleteTableRow('core_resource', 'code', 'adminnotification_setup');
        $this->_model->getConnection()->dropTable($this->_model->getTable('adminnotification_inbox'));
        $this->_model->getConnection()->dropTable($this->_model->getTable('admin_system_messages'));
        /** @var $updater \Magento\Framework\Module\Updater */
        $updater = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Module\Updater');
        try {
            $updater->updateScheme();
            $updater->updateData();
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

    public function testUpdateTableRow()
    {
        $original = $this->_model->getTableRow('core_resource', 'code', 'adminnotification_setup', 'version');
        $this->_model->updateTableRow('core_resource', 'code', 'adminnotification_setup', 'version', 'test');
        $this->assertEquals(
            'test',
            $this->_model->getTableRow('core_resource', 'code', 'adminnotification_setup', 'version')
        );
        $this->_model->updateTableRow('core_resource', 'code', 'adminnotification_setup', 'version', $original);
    }

    /**
     * @expectedException \Zend_Db_Statement_Exception
     */
    public function testGetTableRow()
    {
        $this->assertNotEmpty($this->_model->getTableRow('core_resource', 'code', 'core_setup'));
        $this->_model->getTableRow('core/resource', 'code', 'core_setup');
    }

    /**
     * @expectedException \Zend_Db_Statement_Exception
     */
    public function testDeleteTableRow()
    {
        $this->_model->deleteTableRow('core/resource', 'code', 'integration_test_fixture_setup');
    }

    /**
     * @covers \Magento\Framework\Module\Setup::updateTableRow
     * @expectedException \Zend_Db_Statement_Exception
     */
    public function testUpdateTableRowNameConversion()
    {
        $original = $this->_model->getTableRow('core_resource', 'code', 'core_setup', 'version');
        $this->_model->updateTableRow('core/resource', 'code', 'core_setup', 'version', $original);
    }

    public function testTableExists()
    {
        $this->assertTrue($this->_model->tableExists('store_website'));
        $this->assertFalse($this->_model->tableExists('core/website'));
    }
}
