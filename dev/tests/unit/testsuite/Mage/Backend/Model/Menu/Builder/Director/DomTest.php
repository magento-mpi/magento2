<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_Backend
     * @subpackage  unit_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */

/**
 * Test class for Mage_Backend_Model_Menu_Builder_Director_Dom
 */
class Mage_Backend_Model_Menu_Builder_Director_DomTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Mage_Backend_Model_Menu_Builder_Director_Dom
     */
    protected $_model;

    public function setUp()
    {
        $basePath = realpath(__DIR__)  . '/../../../_files/';
        $path = $basePath . 'menu_merged.xml';
        $domDocument = new DOMDocument();
        $domDocument->load($path);

        $mockCommand = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Menu_Builder_CommandAbstract',
            array(),
            '',
            false
        );

        $factory = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $factory->expects($this->any())->method('getModelInstance')->will($this->returnValue($mockCommand));

        $this->_model = new Mage_Backend_Model_Menu_Builder_Director_Dom(
            array(
                'config' => $domDocument,
                'factory' => $factory
            )
        );
    }

    /**
     * Test __construct if required param missed
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConstructorException()
    {
        new Mage_Backend_Model_Menu_Builder_Director_Dom();
    }

    /**
     * Test __construct if config is no instance of DOMDocument
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConfigInstanceConstructorException()
    {
        $object = $this->getMock('StdClass');
        new Mage_Backend_Model_Menu_Builder_Director_Dom(array('config' => $object, 'factory' => $object));
    }

    /**
     * Test __construct if config is instance of DOMDocument
     */
    public function testValidConfigInstanceConstructor()
    {
        $domDocument = $this->getMock('DOMDocument');
        $factory = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $model = new Mage_Backend_Model_Menu_Builder_Director_Dom(
            array(
                'config' => $domDocument,
                'factory' => $factory,
            )
        );
        unset($model);
    }

    /**
     * Test data extracted from DOMDocument
     */
    public function testExtractData()
    {
        $basePath = realpath(__DIR__)  . '/../../../_files/';
        $expectedData = include ($basePath . 'menu_merged.php');
        $this->assertEquals($expectedData, $this->_model->getExtractedData(), 'Invalid extracted data');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCommandWithInvalidBuilder()
    {
        $object = $this->getMock('StdClass');
        $this->_model->buildMenu($object);
    }

    /**
     * Test command method with valid builder
     */
    public function testCommandWithValidBuilder()
    {
        $builder = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Menu_BuilderAbstract',
            array(),
            '',
            false,
            false,
            true,
            array('processCommand')
        );
        $builder->expects($this->exactly(10))->method('processCommand');
        $this->assertInstanceOf('Mage_Backend_Model_Menu_Builder_DirectorAbstract', $this->_model->buildMenu($builder));
    }
}
