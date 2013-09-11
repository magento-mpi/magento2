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

class Magento_Core_Model_Resource_SetupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Setup
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getResourceModel('\Magento\Core\Model\Resource\Setup',
            array('resourceName' => 'default_setup')
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
        Mage::getResourceModel('\Magento\Core\Model\Resource\Resource')->setDbVersion('adminnotification_setup', false);
        Mage::getResourceModel('\Magento\Core\Model\Resource\Resource')
            ->setDataVersion('adminnotification_setup', false);
        $this->_model->deleteTableRow('core_resource', 'code', 'adminnotification_setup');
        $this->_model->getConnection()->dropTable($this->_model->getTable('adminnotification_inbox'));
        $this->_model->getConnection()->dropTable($this->_model->getTable('admin_system_messages'));
        /** @var $updater \Magento\Core\Model\Db\Updater */
        $updater = Mage::getSingleton('Magento\Core\Model\Db\Updater');
        try {
            $updater->updateScheme();
            $updater->updateData();
        } catch (Exception $e) {
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

    public function testSetDeleteConfigData()
    {
        $select = $this->_model->getConnection()->select()
            ->from($this->_model->getTable('core_config_data'), 'value')
            ->where('path=?', 'my/test/path');

        $this->_model->setConfigData('my/test/path', 'test_value');
        $this->assertEquals('test_value', $this->_model->getConnection()->fetchOne($select));

        $this->_model->deleteConfigData('my/test/path', 'test');
        $this->assertNotEmpty($this->_model->getConnection()->fetchRow($select));

        $this->_model->deleteConfigData('my/test/path');
        $this->assertEmpty($this->_model->getConnection()->fetchRow($select));
    }

    /**
     * @expectedException Zend_Db_Statement_Exception
     */
    public function testGetTableRow()
    {
        $this->assertNotEmpty($this->_model->getTableRow('core_resource', 'code', 'core_setup'));
        $this->_model->getTableRow('core/resource', 'code', 'core_setup');
    }

    /**
     * @expectedException Zend_Db_Statement_Exception
     */
    public function testDeleteTableRow()
    {
        $this->_model->deleteTableRow('core/resource', 'code', 'integration_test_fixture_setup');
    }

    /**
     * @covers \Magento\Core\Model\Resource\Setup::updateTableRow
     * @expectedException Zend_Db_Statement_Exception
     */
    public function testUpdateTableRowNameConversion()
    {
        $original = $this->_model->getTableRow('core_resource', 'code', 'core_setup', 'version');
        $this->_model->updateTableRow('core/resource', 'code', 'core_setup', 'version', $original);
    }

    public function testTableExists()
    {
        $this->assertTrue($this->_model->tableExists('core_website'));
        $this->assertFalse($this->_model->tableExists('core/website'));
    }
}
