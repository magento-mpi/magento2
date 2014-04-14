<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductTypes;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\Config
     */
    protected $model;

    protected function setUp()
    {
        $this->readerMock = $this->getMock(
            'Magento\Catalog\Model\ProductTypes\Config\Reader',
            array(),
            array(),
            '',
            false
        );
        $this->cacheMock = $this->getMock('Magento\Config\CacheInterface');
    }

    /**
     * @dataProvider getTypeDataProvider
     *
     * @param array $value
     * @param mixed $expected
     */
    public function testGetType($value, $expected)
    {
        $this->cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($value)));
        $this->model = new \Magento\Catalog\Model\ProductTypes\Config($this->readerMock, $this->cacheMock, 'cache_id');
        $this->assertEquals($expected, $this->model->getType('global'));
    }

    public function getTypeDataProvider()
    {
        return array(
            'global_key_exist' => array(array('types' => array('global' => 'value')), 'value'),
            'return_default_value' => array(array('types' => array('some_key' => 'value')), array())
        );
    }

    public function testGetAll()
    {
        $expected = array('Expected Data');
        $this->cacheMock->expects(
            $this->once()
        )->method(
            'load'
        )->will(
            $this->returnValue(serialize(array('types' => $expected)))
        );
        $this->model = new \Magento\Catalog\Model\ProductTypes\Config($this->readerMock, $this->cacheMock, 'cache_id');
        $this->assertEquals($expected, $this->model->getAll());
    }

    public function testIsProductSet()
    {
        $this->cacheMock->expects($this->once())->method('load')->will($this->returnValue(serialize(array())));
        $this->model = new \Magento\Catalog\Model\ProductTypes\Config($this->readerMock, $this->cacheMock, 'cache_id');

        $this->assertEquals(false, $this->model->isProductSet('typeId'));
    }
}
