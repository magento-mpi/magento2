<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Generator_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Generic object manager factory interface
     */
    const FACTORY_INTERFACE = '\Magento_ObjectManager_Factory';

    /**#@+
     * Source and result class parameters
     */
    const SOURCE_CLASS = 'ClassName';
    const RESULT_CLASS = 'ClassNameFactory';
    const RESULT_FILE  = 'ClassNameFactory.php';
    /**#@-*/

    /**
     * Generated code
     */
    const CODE = "a = 1;";

    /**
     * Expected factory methods
     *
     * @var array
     */
    protected static $_expectedMethods = array(
        array(
            'name'       => '__construct',
            'parameters' =>
            array(
                array(
                    'name' => 'objectManager',
                    'type' => '\\Magento_ObjectManager',
                ),
            ),
            'body'       => '$this->_objectManager = $objectManager;',
            'docblock'   =>
            array(
                'shortDescription' => 'Factory constructor',
                'tags'             =>
                array(
                    array(
                        'name'        => 'param',
                        'description' => '\\Magento_ObjectManager $objectManager',
                    ),
                ),
            ),
        ),
        array(
            'name'       => 'createFromArray',
            'parameters' =>
            array(
                array(
                    'name'         => 'data',
                    'type'         => 'array',
                    'defaultValue' =>
                    array(),
                ),
            ),
            'body'       => 'return $this->_objectManager->create(self::CLASS_NAME, $data, false);',
            'docblock'   =>
            array(
                'shortDescription' => 'Create class instance with specified parameters',
                'tags'             =>
                array(
                    array(
                        'name'        => 'param',
                        'description' => 'array $data',
                    ),
                    array(
                        'name'        => 'return',
                        'description' => '\\ClassName',
                    ),
                ),
            ),
        ),
    );

    /**
     * Model under test
     *
     * @var Magento_Di_Generator_Factory
     */
    protected $_model;

    protected function setUp()
    {
        $ioObjectMock = $this->getMock('Magento_Di_Generator_Io',
            array('getResultFileName', 'makeGenerationDirectory', 'makeResultFileDirectory', 'fileExists',
                'writeResultFile'
            ), array(), '', false
        );
        $ioObjectMock->expects($this->any())
            ->method('getResultFileName')
            ->will($this->returnValue(self::RESULT_FILE));
        $ioObjectMock->expects($this->any())
            ->method('makeGenerationDirectory')
            ->will($this->returnValue(true));
        $ioObjectMock->expects($this->any())
            ->method('makeResultFileDirectory')
            ->will($this->returnValue(true));
        $ioObjectMock->expects($this->any())
            ->method('fileExists')
            ->will($this->returnValue(false));
        $ioObjectMock->expects($this->once())
            ->method('writeResultFile')
            ->with(self::RESULT_FILE, self::CODE);

        $codeGeneratorMock = $this->getMock('Magento_Di_Generator_CodeGenerator_Zend',
            array('setImplementedInterfaces', 'setName', 'addProperties', 'addMethods', 'setClassDocBlock', 'generate'),
            array(), '', false
        );
        $codeGeneratorMock->expects($this->once())
            ->method('setImplementedInterfaces')
            ->with(array(self::FACTORY_INTERFACE))
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('setName')
            ->with(self::RESULT_CLASS)
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('addProperties')
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('addMethods')
            ->with(self::$_expectedMethods)
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('setClassDocBlock')
            ->with($this->isType('array'))
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('generate')
            ->will($this->returnValue(self::CODE));

        $autoLoaderMock = $this->getMock('Magento_Autoload', array('classExists'), array(), '', false);
        $autoLoaderMock->expects($this->at(0))
            ->method('classExists')
            ->with(self::SOURCE_CLASS)
            ->will($this->returnValue(true));
        $autoLoaderMock->expects($this->at(1))
            ->method('classExists')
            ->with(self::RESULT_CLASS)
            ->will($this->returnValue(false));

        /** @var $ioObjectMock Magento_Di_Generator_Io */
        /** @var $codeGeneratorMock Magento_Di_Generator_CodeGenerator_Zend */
        /** @var $autoLoaderMock Magento_Autoload */
        $this->_model = new Magento_Di_Generator_Factory(self::SOURCE_CLASS, self::RESULT_CLASS, $ioObjectMock,
            $codeGeneratorMock, $autoLoaderMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @covers Magento_Di_Generator_Factory::_generateCode
     * @covers Magento_Di_Generator_Factory::_getClassMethods
     */
    public function testGenerate()
    {
        $result = $this->_model->generate();
        var_dump($this->_model->getErrors());
        $this->assertTrue($result);
        $this->assertEmpty($this->_model->getErrors());
    }
}
