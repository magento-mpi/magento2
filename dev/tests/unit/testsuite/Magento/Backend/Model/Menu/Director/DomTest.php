<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Backend_Model_Menu_Director_Dom
 */
class Magento_Backend_Model_Menu_Director_DomTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Magento_Backend_Model_Menu_Director_Dom
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loggerMock;

    public function setUp()
    {
        $basePath = realpath(__DIR__)  . '/../../_files/';
        $path = $basePath . 'menu_merged.xml';
        $domDocument = new DOMDocument();
        $domDocument->load($path);

        $mockCommand = $this->getMockForAbstractClass(
            'Magento_Backend_Model_Menu_Builder_CommandAbstract',
            array(),
            '',
            false,
            true,
            true,
            array('getId')
        );

        $factory = $this->getMock('Magento_ObjectManager');
        $factory->expects($this->any())->method('create')->will($this->returnValue($mockCommand));

        $this->_loggerMock = $this->getMock('Magento_Core_Model_Logger', array('log'), array(), '', false);

        $this->_model = new Magento_Backend_Model_Menu_Director_Dom(
            $domDocument,
            $factory,
            $this->_loggerMock
        );
    }

    /**
     * Test data extracted from DOMDocument
     */
    public function testExtractData()
    {
        $basePath = realpath(__DIR__)  . '/../../_files/';
        $expectedData = include ($basePath . 'menu_merged.php');
        $this->assertEquals($expectedData, $this->_model->getExtractedData(), 'Invalid extracted data');
    }

    /**
     * Test command method with valid builder
     */
    public function testCommandWithValidBuilder()
    {
        $builder = $this->getMock('Magento_Backend_Model_Menu_Builder', array('processCommand'), array(), '', false);
        $builder->expects($this->exactly(8))->method('processCommand');
        $this->assertInstanceOf('Magento_Backend_Model_Menu_DirectorAbstract', $this->_model->buildMenu($builder));
    }

    public function testCommandLogging()
    {
        $this->_loggerMock->expects($this->exactly(4))->method('log');
        $builder = $this->getMock('Magento_Backend_Model_Menu_Builder', array(), array(), '', false);
        $this->_model->buildMenu($builder);
    }
}
