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

class Magento_Di_Generator_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Source and result class parameters
     */
    const SOURCE_CLASS = 'Magento\Di\Generator\TestAsset\SourceClass';
    const RESULT_CLASS = 'SourceClassProxy';
    const RESULT_FILE  = 'SourceClassProxy.php';
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
            'name'       => 'publicChildMethod',
            'parameters' =>
            array(
                array(
                    'name'              => 'classGenerator',
                    'passedByReference' => false,
                    'type'              => '\\Zend\\Code\\Generator\\ClassGenerator',
                ),
                array(
                    'name'              => 'param1',
                    'passedByReference' => false,
                    'defaultValue'      => '',
                ),
                array(
                    'name'              => 'param2',
                    'passedByReference' => false,
                    'defaultValue'      => '\\\\',
                ),
                array(
                    'name'              => 'param3',
                    'passedByReference' => false,
                    'defaultValue'      => '\\\'',
                ),
                array(
                    'name'              => 'array',
                    'passedByReference' => false,
                    'type'              => 'array',
                    'defaultValue'      =>
                    array(),
                ),
            ),
            'body'       => 'return $this->_objectManager->get(self::CLASS_NAME)->publicChildMethod($classGenerator, $param1, $param2, $param3, $array);',
            'docblock'   =>
            array(
                'shortDescription' => '{@inheritdoc}',
            ),
        ),
        array(
            'name'       => 'publicMethodWithReference',
            'parameters' =>
            array(
                array(
                    'name'              => 'classGenerator',
                    'passedByReference' => true,
                    'type'              => '\\Zend\\Code\\Generator\\ClassGenerator',
                ),
                array(
                    'name'              => 'param1',
                    'passedByReference' => true,
                    'defaultValue'      => '',
                ),
                array(
                    'name'              => 'array',
                    'passedByReference' => true,
                    'type'              => 'array',
                ),
            ),
            'body'       => 'return $this->_objectManager->get(self::CLASS_NAME)->publicMethodWithReference($classGenerator, $param1, $array);',
            'docblock'   =>
            array(
                'shortDescription' => '{@inheritdoc}',
            ),
        ),
        array(
            'name'       => 'publicChildWithoutParameters',
            'parameters' =>
            array(),
            'body'       => 'return $this->_objectManager->get(self::CLASS_NAME)->publicChildWithoutParameters();',
            'docblock'   =>
            array(
                'shortDescription' => '{@inheritdoc}',
            ),
        ),
        array(
            'name'       => 'publicParentMethod',
            'parameters' =>
            array(
                array(
                    'name'              => 'docBlockGenerator',
                    'passedByReference' => false,
                    'type'              => '\\Zend\\Code\\Generator\\DocBlockGenerator',
                ),
                array(
                    'name'              => 'param1',
                    'passedByReference' => false,
                    'defaultValue'      => '',
                ),
                array(
                    'name'              => 'param2',
                    'passedByReference' => false,
                    'defaultValue'      => '\\\\',
                ),
                array(
                    'name'              => 'param3',
                    'passedByReference' => false,
                    'defaultValue'      => '\\\'',
                ),
                array(
                    'name'              => 'array',
                    'passedByReference' => false,
                    'type'              => 'array',
                    'defaultValue'      =>
                    array(),
                ),
            ),
            'body'       => 'return $this->_objectManager->get(self::CLASS_NAME)->publicParentMethod($docBlockGenerator, $param1, $param2, $param3, $array);',
            'docblock'   =>
            array(
                'shortDescription' => '{@inheritdoc}',
            ),
        ),
        array(
            'name'       => 'publicParentWithoutParameters',
            'parameters' =>
            array(),
            'body'       => 'return $this->_objectManager->get(self::CLASS_NAME)->publicParentWithoutParameters();',
            'docblock'   =>
            array(
                'shortDescription' => '{@inheritdoc}',
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
            array('setExtendedClass', 'setName', 'addProperties', 'addMethods', 'setClassDocBlock', 'generate'),
            array(), '', false
        );
        $codeGeneratorMock->expects($this->once())
            ->method('setExtendedClass')
            ->with('\\'. self::SOURCE_CLASS)
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
        $this->_model = new Magento_Di_Generator_Proxy(self::SOURCE_CLASS, self::RESULT_CLASS, $ioObjectMock,
            $codeGeneratorMock, $autoLoaderMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @covers Magento_Di_Generator_Proxy::_getClassMethods
     * @covers Magento_Di_Generator_Proxy::_generateCode
     * @covers Magento_Di_Generator_Proxy::_getMethodInfo
     * @covers Magento_Di_Generator_Proxy::_getMethodParameterInfo
     * @covers Magento_Di_Generator_Proxy::_escapeDefaultValue
     * @covers Magento_Di_Generator_Proxy::_getMethodBody
     */
    public function testGenerate()
    {
        $result = $this->_model->generate();
        $this->assertTrue($result);
        $this->assertEmpty($this->_model->getErrors());
    }
}
