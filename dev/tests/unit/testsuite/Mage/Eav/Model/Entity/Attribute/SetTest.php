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

        $helper = $this->getMock('Mage_Eav_Helper_Data', array('__'));
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $this->_model = new Mage_Eav_Model_Entity_Attribute_Set(array(
            'resource'  => $resource
        ));
        $this->_model->setHelper($helper);
    }

    /**
     * @param string $attributeSetName
     * @param string $exceptionMessage
     * @dataProvider invalidAttributeSetDataProvider
     */
    public function testValidateWithExistedNameThrowsException($attributeSetName, $exceptionMessage)
    {
        $this->_model->getResource()
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(false));

        $this->setExpectedException('Mage_Eav_Exception', $exceptionMessage);
        $this->_model->setAttributeSetName($attributeSetName);
        $this->_model->validate();
    }

    public function testValidateWithNonExistedValidNameReturnsSuccess()
    {
        $this->_model->getResource()
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));

        $this->_model->setAttributeSetName('non_existed_name');
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
            array('existed_name', 'Attribute set with the "%s" name already exists.')
        );
    }
}
