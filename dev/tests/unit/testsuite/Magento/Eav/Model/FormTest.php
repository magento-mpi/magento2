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
 * Test for \Magento\Eav\Model\Form
 */
class Magento_Eav_Model_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Form
     */
    protected $_model = null;

    /**
     * @var array
     */
    protected $_attributes = null;

    /**
     * @var array
     */
    protected $_systemAttribute = null;

    /**
     * @var array
     */
    protected $_userAttribute = null;

    /**
     * @var \Magento\Object
     */
    protected $_entity = null;

    /**
     * Initialize form
     */
    protected function setUp()
    {
        $this->_model = $this->getMockBuilder('Magento\Eav\Model\Form')
            ->setMethods(array('_getFilteredFormAttributeCollection', '_getValidator', 'getEntity'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_userAttribute = new \Magento\Object(array(
            'is_user_defined' => true,
            'attribute_code' => 'attribute_visible_user',
            'is_visible' => true
        ));
        $this->_systemAttribute = new \Magento\Object(array(
            'is_user_defined' => false,
            'attribute_code' => 'attribute_invisible_system',
            'is_visible' => false
        ));
        $this->_attributes = array($this->_userAttribute, $this->_systemAttribute);
        $this->_model->expects($this->any())
            ->method('_getFilteredFormAttributeCollection')
            ->will($this->returnValue($this->_attributes));

        $this->_entity = new \Magento\Object(array('id' => 1, 'attribute_visible_user' => 'abc'));
        $this->_model->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($this->_entity));
    }

    /**
     * Unset form
     */
    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Test getAttributes
     */
    public function testGetAttributes()
    {
        $expected = array(
            'attribute_visible_user' => $this->_userAttribute,
            'attribute_invisible_system' => $this->_systemAttribute
        );
        $this->assertEquals($expected, $this->_model->getAttributes());
    }

    /**
     * Test getUserAttributes
     */
    public function testGetUserAttributes()
    {
        $expected = array(
            'attribute_visible_user' => $this->_userAttribute
        );
        $this->assertEquals($expected, $this->_model->getUserAttributes());
    }

    /**
     * Test getSystemAttributes
     */
    public function testGetSystemAttributes()
    {
        $expected = array(
            'attribute_invisible_system' => $this->_systemAttribute
        );
        $this->assertEquals($expected, $this->_model->getSystemAttributes());
    }

    /**
     * Test getAllowedAttributes
     */
    public function testGetAllowedAttributes()
    {
        $expected = array(
            'attribute_visible_user' => $this->_userAttribute
        );
        $this->assertEquals($expected, $this->_model->getAllowedAttributes());
    }

    /**
     * Test validateData method
     *
     * @dataProvider validateDataProvider
     *
     * @param bool $isValid
     * @param bool|array $expected
     * @param null|array $messages
     */
    public function testValidateDataPassed($isValid, $expected, $messages = null)
    {
        $validator = $this->getMockBuilder('Magento\Eav\Model\Validator\Attribute\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isValid', 'getMessages'))
            ->getMock();
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue($isValid));
        if ($messages) {
            $validator->expects($this->once())
                ->method('getMessages')
                ->will($this->returnValue($messages));
        } else {
            $validator->expects($this->never())
                ->method('getMessages');
        }

        $this->_model->expects($this->once())
            ->method('_getValidator')
            ->will($this->returnValue($validator));

        $data = array('test' => true);
        $this->assertEquals($expected, $this->_model->validateData($data));
    }

    /**
     * Data provider for testValidateDataPassed
     *
     * @return array
     */
    public function validateDataProvider()
    {
        return array(
            'is_valid' => array(
                true, true, null
            ),
            'is_invalid' => array(
                false, array('Invalid'), array('attribute_visible_user' => array('Invalid'))
            ),
        );
    }
}
