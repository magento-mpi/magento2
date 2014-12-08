<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Cache\State;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Cache\State\Options
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Cache\State\Options'
        );
    }

    public function testGetTable()
    {
        $this->_resourceMock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Resource',
            ['tablePrefix' => 'prefix_']
        );

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Cache\State\Options',
            ['resource' => $this->_resourceMock]
        );
        $this->assertEquals('prefix_core_cache_option', $this->_model->getTable('core_cache_option'));
        $this->assertEquals('prefix_core_cache_option', $this->_model->getTable(['core_cache', 'option']));
    }

    public function testUniqueFields()
    {
        $fields = ['field' => 'text'];
        $this->_model->addUniqueField($fields);
        $this->assertEquals([$fields], $this->_model->getUniqueFields());
        $this->_model->resetUniqueField();
        $this->assertEquals([], $this->_model->getUniqueFields());
    }

    public function testHasDataChanged()
    {
        $object = new \Magento\Framework\Object(['code' => 'value1', 'value' => 'value2']);
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
