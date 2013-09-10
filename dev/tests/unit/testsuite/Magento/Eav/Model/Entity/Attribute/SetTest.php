<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Eav_Model_Entity_Attribute_Set
 */
class Magento_Eav_Model_Entity_Attribute_SetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Eav_Model_Entity_Attribute_Set
     */
    protected $_model;

    protected function setUp()
    {
        $resource = $this->getMock('Magento_Eav_Model_Resource_Entity_Attribute_Set', array(), array(), '', false);

        $arguments = array(
            'resource'  => $resource,
        );
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento_Eav_Model_Entity_Attribute_Set', $arguments);
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

        $this->setExpectedException('Magento_Eav_Exception', $exceptionMessage);
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
            array('existing_name', 'An attribute set with the "existing_name" name already exists.')
        );
    }
}
