<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Eav_Model_Entity_Attribute_Set
 */
class Mage_Eav_Model_Entity_Attribute_SetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Eav_Model_Entity_Attribute_Set
     */
    protected $_model;

    protected function setUp()
    {
        $resource = $this->getMock('Mage_Eav_Model_Resource_Entity_Attribute_Set', array(), array(), '', false);

        $helper = $this->getMock('Mage_Eav_Helper_Data', array('__'), array(), '', false, false);
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $arguments = array(
            'resource'  => $resource,
            'data'      => array('helper' => $helper)
        );
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getModel('Mage_Eav_Model_Entity_Attribute_Set', $arguments);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }


    /**
     * @param string $attributeSetName
     * @param string $exceptionMessage
     * @dataProvider invalidAttributeSetDataProvider
     */
    public function testValidateWithExistingName($attributeSetName, $exceptionMessage)
    {
        $this->_model->getResource()
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(false));

        $this->setExpectedException('Mage_Eav_Exception', $exceptionMessage);
        $this->_model->setAttributeSetName($attributeSetName);
        $this->_model->validate();
    }

    public function testValidateWithNonexistentValidName()
    {
        $this->_model->getResource()
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));

        $this->_model->setAttributeSetName('nonexistent_name');
        $this->assertTrue($this->_model->validate());
    }

    /**
     * Retrieve data for invalid
     *
     * @return array
     */
    public function invalidAttributeSetDataProvider()
    {
        return array(
            array('', 'Attribute set name is empty.'),
            array('existing_name', 'Attribute set with the "%s" name already exists.')
        );
    }

    /**
     * @param int $entityTypeId
     * @dataProvider validGetMinimalAttrSetIdDataProvider
     */
    public function testGetMinimalAttrSetId($entityTypeId)
    {
        $this->_model->getResource()
            ->expects($this->once())
            ->method('getMinimalAttrSetId')
            ->with($this->equalTo($entityTypeId))
            ->will($this->returnValue($entityTypeId));

        $result = $this->_model->getMinimalAttrSetId($entityTypeId);
        $this->assertEquals($entityTypeId, $result);
    }

    /**
     * @param int $entityTypeId
     * @dataProvider invalidGetMinimalAttrSetIdDataProvider
     * @expectedException InvalidArgumentException
     */
    public function testGetMinimalAttrSetIdExceptionThrows($entityTypeId)
    {
        $result = $this->_model->getMinimalAttrSetId($entityTypeId);
    }

    /**
     * Valid Entity Type Ids Data Provider
     *
     * @return array
     */
    public function validGetMinimalAttrSetIdDataProvider()
    {
        return array(
            array(1),
            array(10),
            array(100),
        );
    }

    /**
     *  Invalid Entity Type Ids Data Provider
     *
     * @return array
     */
    public function invalidGetMinimalAttrSetIdDataProvider()
    {
        return array(
            array('failure test'),
            array(null),
            array(array()),
        );
    }
}
