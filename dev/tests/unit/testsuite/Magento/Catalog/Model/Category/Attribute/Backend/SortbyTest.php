<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Category_Attribute_Backend_SortbyTest extends PHPUnit_Framework_TestCase
{
    const DEFAULT_ATTRIBUTE_CODE = 'attribute_name';

    /**
     * @var \Magento\Catalog\Model\Category\Attribute\Backend\Sortby
     */
    protected $_model;

    protected function setUp()
    {
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $this->_model = new \Magento\Catalog\Model\Category\Attribute\Backend\Sortby($coreStoreConfig);
        $attribute = $this->getMockForAbstractClass('Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array(), '', false, true, true, array('getName')
        );
        $attribute->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::DEFAULT_ATTRIBUTE_CODE));
        $this->_model->setAttribute($attribute);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider beforeSaveDataProvider
     */
    public function testBeforeSave($data, $expected)
    {
        $object = new \Magento\Object($data);
        $this->_model->beforeSave($object);
        $this->assertTrue($object->hasData(self::DEFAULT_ATTRIBUTE_CODE));
        $this->assertSame($expected, $object->getData(self::DEFAULT_ATTRIBUTE_CODE));
    }

    public function beforeSaveDataProvider()
    {
        return array(
            'attribute with specified value' => array(
                array(self::DEFAULT_ATTRIBUTE_CODE => 'test_value'),
                'test_value',
            ),
            'attribute with default value' => array(
                array(self::DEFAULT_ATTRIBUTE_CODE => null),
                null,
            ),
            'attribute does not exist' => array(
                array(),
                false,
            ),
        );
    }
}
