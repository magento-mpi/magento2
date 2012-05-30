<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API2 data helper
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Helper_DataTest extends Mage_PHPUnit_TestCase
{
    /**
     * API2 data helper
     *
     * @var Mage_Api2_Helper_Data
     */
    protected $_helper;

    /**
     * Rule model mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeResourceMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_helper = Mage::helper('Mage_Api2_Helper_Data');
        $this->_attributeResourceMock = $this
            ->getResourceModelMockBuilder('Mage_Api2_Model_Resource_Acl_Filter_Attribute')
            ->setMethods(array('getAllowedAttributes', 'isAllAttributesAllowed'))
            ->getMock();
    }

    /**
     * Test get allowed attributes
     */
    public function testGetAllowedAttributes()
    {
        $this->_attributeResourceMock->expects($this->once())
            ->method('getAllowedAttributes')
            ->will($this->returnValue('a,b,c'));

        $this->assertSame(array('a', 'b', 'c'), $this->_helper->getAllowedAttributes(1, 2, 4));
        $this->assertTrue(true);
    }

    /**
     * Test get allowed attributes of a rule which has no attributes
     */
    public function testGetAllowedAttributesEmpty()
    {
        $this->_attributeResourceMock->expects($this->once())
            ->method('getAllowedAttributes')
            ->will($this->returnValue(false));

        $this->assertSame(array(), $this->_helper->getAllowedAttributes(1, 2, 4));
    }

    /**
     * Test check if ALL attributes allowed
     */
    public function testIsAllAttributesAllowed()
    {
        $this->_attributeResourceMock->expects($this->once())
            ->method('isAllAttributesAllowed');

        $this->_helper->isAllAttributesAllowed('a');
    }
}
