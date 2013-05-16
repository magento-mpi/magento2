<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Customer_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Customer_Helper_Data
     */
    protected $_customerHelper;

    /**
     * Set up a fixture
     */
    public function setUp()
    {
        $abstractHelperMock = $this->getMockBuilder('Mage_Core_Helper_Abstract')->disableOriginalConstructor()->getMock();
        $abstractHelperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        $objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')->getMock();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($abstractHelperMock));
        Mage::reset();
        Mage::setObjectManager($objectManagerMock);
    }

    /**
     * Clean up after test
     */
    public function tearDown()
    {
        Mage::reset();
    }

    /**
     * @test
     */
    public function getAttributeInputTypesDefault()
    {
        $contextMock = $this->getMockBuilder('Mage_Core_Helper_Context')->disableOriginalConstructor()->getMock();
        $helper = new Saas_Customer_Helper_Data($contextMock);
        $types = $helper->getAttributeInputTypes();

        $this->assertArrayNotHasKey('file', $types);
        $this->assertArrayNotHasKey('image', $types);
    }

    /**
     * @param $argument
     * @dataProvider getAttributeInputTypesDataProvider
     * @test
     */
    public function getAttributeInputTypesParams($argument)
    {
        $contextMock = $this->getMockBuilder('Mage_Core_Helper_Context')->disableOriginalConstructor()->getMock();
        $helper = new Saas_Customer_Helper_Data($contextMock);

        $this->assertEquals(array(), $helper->getAttributeInputTypes($argument));
    }

    public function getAttributeInputTypesDataProvider()
    {
        return array(
            array('file'),
            array('image'),
        );
    }
}
