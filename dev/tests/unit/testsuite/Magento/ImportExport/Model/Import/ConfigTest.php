<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var string
     */
    protected $_cacheId = 'some_id';

    /**
     * @var \Magento\ImportExport\Model\Import\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock(
            'Magento\ImportExport\Model\Import\Config\Reader',
            array(),
            array(),
            '',
            false
        );
        $this->_configScopeMock = $this->getMock('Magento\Framework\Config\CacheInterface');
    }

    /**
     * @param array $value
     * @param null|string $expected
     * @dataProvider getEntitiesDataProvider
     */
    public function testGetEntities($value, $expected)
    {
        $this->_configScopeMock->expects(
            $this->any()
        )->method(
            'load'
        )->with(
            $this->_cacheId
        )->will(
            $this->returnValue(false)
        );
        $this->_readerMock->expects($this->any())->method('read')->will($this->returnValue($value));
        $this->_model = new \Magento\ImportExport\Model\Import\Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheId
        );
        $this->assertEquals($expected, $this->_model->getEntities('entities'));
    }

    public function getEntitiesDataProvider()
    {
        return array(
            'entities_key_exist' => array(array('entities' => 'value'), 'value'),
            'return_default_value' => array(array('key_one' => 'value'), null)
        );
    }

    /**
     * @param array $configData
     * @param string $entity
     * @param string[] $expectedResult
     * @dataProvider getEntityTypesDataProvider
     */
    public function testGetEntityTypes($configData, $entity, $expectedResult)
    {
        $this->_configScopeMock->expects(
            $this->any()
        )->method(
            'load'
        )->with(
            $this->_cacheId
        )->will(
            $this->returnValue(false)
        );
        $this->_readerMock->expects($this->any())->method('read')->will($this->returnValue($configData));
        $this->_model = new \Magento\ImportExport\Model\Import\Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheId
        );
        $this->assertEquals($expectedResult, $this->_model->getEntityTypes($entity));
    }

    public function getEntityTypesDataProvider()
    {
        return [
            'valid type' => [
                [
                    'entities' => [
                        'catalog_product' => [
                            'types' => ['configurable', 'simple']
                        ]
                    ]
                ],
                'catalog_product',
                ['configurable', 'simple']
            ],
            'not existing entity' => [
                [
                    'entities' => [
                        'catalog_product' => [
                            'types' => ['configurable', 'simple']
                        ]
                    ]
                ],
                'not existing entity',
                []
            ],
        ];
    }
}
