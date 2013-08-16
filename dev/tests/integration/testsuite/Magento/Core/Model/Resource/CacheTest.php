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

class Magento_Core_Model_Resource_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Cache
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getResourceModel('Magento_Core_Model_Resource_Cache');
    }

    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testGetTable()
    {
        $this->assertEquals('prefix_core_cache_option', $this->_model->getTable('core_cache_option'));
        $this->assertEquals('prefix_core_cache_option', $this->_model->getTable(array('core_cache', 'option')));
    }

    public function testUniqueFields()
    {
        $fields = array('field' => 'text');
        $this->_model->addUniqueField($fields);
        $this->assertEquals(array($fields), $this->_model->getUniqueFields());
        $this->_model->resetUniqueField();
        $this->assertEquals(array(), $this->_model->getUniqueFields());
    }

    public function testHasDataChanged()
    {
        $object = new Magento_Object(
            array(
                'code'  => 'value1',
                'value' => 'value2'
            )
        );
        $this->assertTrue($this->_model->hasDataChanged($object));

        $object->setOrigData();
        $this->assertFalse($this->_model->hasDataChanged($object));
        $object->setData('code', 'v1');
        $this->assertTrue($this->_model->hasDataChanged($object));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetSaveAllOptions()
    {
        $options = $this->_model->getAllOptions();
        $this->assertArrayNotHasKey('test_option', $options);
        $options['test_option'] = 1;
        $this->_model->saveAllOptions($options);
        $this->assertEquals($options, $this->_model->getAllOptions());
    }
}
