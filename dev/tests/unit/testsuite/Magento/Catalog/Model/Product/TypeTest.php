<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectHelper;

    /**
     * Product types config values
     *
     * @var array
     */
    protected $_productTypes = array(
        'type_id_1' => array('label' => 'label_1'),
        'type_id_2' => array('label' => 'label_2'),
        'type_id_3' => array('label' => 'label_3', 'model' => 'some_model', 'composite' => 'some_type'),
        'type_id_4' => array('label' => 'label_4', 'composite' => false),
    );

    /**
     * @var Magento_Catalog_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $config = $this->getMock('Magento_Catalog_Model_ProductTypes_ConfigInterface');

        $config->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($this->_productTypes));

        $this->_model = $this->_objectHelper->getObject('Magento_Catalog_Model_Product_Type', array(
            'config' => $config,
        ));
    }

    public function testGetTypes()
    {
        $property = new ReflectionProperty($this->_model, '_types');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($this->_model));
        $this->assertEquals($this->_productTypes, $this->_model->getTypes());
    }

    public function testGetOptionArray()
    {
        $this->assertEquals($this->_getOptions(), $this->_model->getOptionArray());
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $options = array();
        foreach ($this->_productTypes as $typeId => $type) {
            $options[$typeId] = __($type['label']);
        }
        return $options;
    }

    public function testGetAllOption()
    {
        $res[] = array('value' => '', 'label' => '');
        foreach ($this->_getOptions() as $index => $value) {
            $res[] = array(
                'value' => $index,
                'label' => $value
            );
        }
        $this->assertEquals($res, $this->_model->getAllOptions());
    }


    public function testGetOptionText()
    {
        $options = $this->_getOptions();
        $this->assertEquals($options['type_id_3'], $this->_model->getOptionText('type_id_3'));
        $this->assertEquals($options['type_id_1'], $this->_model->getOptionText('type_id_1'));
        $this->assertNotEquals($options['type_id_1'], $this->_model->getOptionText('type_id_4'));
        $this->assertNull($this->_model->getOptionText('not_exist'));
    }

    public function testGetCompositeTypes()
    {
        $property = new ReflectionProperty($this->_model, '_compositeTypes');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($this->_model));

        $this->assertEquals(array('type_id_3'), $this->_model->getCompositeTypes());
    }

    public function testGetTypesByPriority()
    {
        $expected = array();
        foreach ($this->_productTypes as $typeId => $type) {
            $type['label'] = __($type['label']);
            $options[$typeId] = $type;
        }

        $expected['type_id_4'] = $options['type_id_4'];
        $expected['type_id_2'] = $options['type_id_2'];
        $expected['type_id_1'] = $options['type_id_1'];
        $expected['type_id_3'] = $options['type_id_3'];

        $this->assertEquals($expected, $this->_model->getTypesByPriority());
    }
}

