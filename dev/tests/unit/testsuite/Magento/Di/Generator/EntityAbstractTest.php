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

class Magento_Di_Generator_EntityAbstractTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Source and result class parameters
     */
    const SOURCE_CLASS     = 'Varien_Object';
    const RESULT_CLASS     = 'Varien_Object_MyResult';
    const RESULT_FILE      = 'MyResult/MyResult.php';
    const RESULT_DIRECTORY = 'MyResult';
    /**#@-*/

    /**
     * Basic code generation directory
     */
    const GENERATION_DIRECTORY = 'generation';

    /**#@+
     * Generated code before and after code style fix
     */
    const SOURCE_CODE = "a = 1; b = array (); {\n\n some source code \n\n}";
    const RESULT_CODE = "a = 1; b = array(); {\n some source code \n}";
    /**#@-*/

    /**
     * Model under test
     *
     * @var Magento_Di_Generator_EntityAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Magento_Di_Generator_EntityAbstract');
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testConstruct()
    {
        // without parameters
        $this->assertAttributeEmpty('_sourceClassName', $this->_model);
        $this->assertAttributeEmpty('_resultClassName', $this->_model);
        $this->assertAttributeInstanceOf('Magento_Di_Generator_Io', '_ioObject', $this->_model);
        $this->assertAttributeInstanceOf('Magento_Di_Generator_CodeGenerator_Zend', '_classGenerator', $this->_model);
        $this->assertAttributeInstanceOf('Magento_Autoload', '_autoloader', $this->_model);

        // with source class name
        $this->_model = $this->getMockForAbstractClass(
            'Magento_Di_Generator_EntityAbstract', array(self::SOURCE_CLASS)
        );
        $this->assertAttributeEquals(self::SOURCE_CLASS, '_sourceClassName', $this->_model);
        $this->assertAttributeEquals(self::SOURCE_CLASS . 'Abstract', '_resultClassName', $this->_model);

        // with all arguments
        $ioObject      = $this->getMock('Magento_Di_Generator_Io', array(), array(), '', false);
        $codeGenerator = $this->getMock('Magento_Di_Generator_CodeGenerator_Zend', array(), array(), '', false);
        $autoloader    = $this->getMock('Magento_Autoload', array(), array(), '', false);

        $this->_model = $this->getMockForAbstractClass(
            'Magento_Di_Generator_EntityAbstract',
            array(self::SOURCE_CLASS, self::RESULT_CLASS, $ioObject, $codeGenerator, $autoloader)
        );
        $this->assertAttributeEquals(self::RESULT_CLASS, '_resultClassName', $this->_model);
        $this->assertAttributeEquals($ioObject, '_ioObject', $this->_model);
        $this->assertAttributeEquals($codeGenerator, '_classGenerator', $this->_model);
        $this->assertAttributeEquals($autoloader, '_autoloader', $this->_model);
    }

    /**
     * Data provider for testGenerate method
     *
     * @return array
     */
    public function generateDataProvider()
    {
        return array(
            'no_source_class' => array(
                '$arguments' => $this->_prepareMocksForValidateData(false),
                '$errors' => array('Source class ' . self::SOURCE_CLASS . ' doesn\'t exist.')
            ),
            'result_class_exists' => array(
                '$arguments' => $this->_prepareMocksForValidateData(true, true),
                '$errors' => array('Result class ' . self::RESULT_CLASS . ' already exists.')
            ),
            'cant_create_generation_directory' => array(
                '$arguments' => $this->_prepareMocksForValidateData(true, false, false),
                '$errors' => array('Can\'t create directory ' . self::GENERATION_DIRECTORY . '.')
            ),
            'cant_create_result_directory' => array(
                '$arguments' => $this->_prepareMocksForValidateData(true, false, true, false),
                '$errors' => array('Can\'t create directory ' . self::RESULT_DIRECTORY . '.')
            ),
            'result_file_exists' => array(
                '$arguments' => $this->_prepareMocksForValidateData(true, false, true, true, true),
                '$errors' => array('Result file ' . self::RESULT_FILE . ' already exists.')
            ),
            'generate_no_data' => array(
                '$arguments' => $this->_prepareMocksForGenerateCode(false),
                '$errors' => array('Can\'t generate source code.')
            ),
            'generate_ok' => array(
                '$arguments' => $this->_prepareMocksForGenerateCode(true),
            ),
        );
    }

    /**
     * @param array $arguments
     * @param array $errors
     *
     * @dataProvider generateDataProvider
     * @covers Magento_Di_Generator_EntityAbstract::generate
     * @covers Magento_Di_Generator_EntityAbstract::getErrors
     * @covers Magento_Di_Generator_EntityAbstract::_getSourceClassName
     * @covers Magento_Di_Generator_EntityAbstract::_getResultClassName
     * @covers Magento_Di_Generator_EntityAbstract::_getDefaultResultClassName
     * @covers Magento_Di_Generator_EntityAbstract::_generateCode
     * @covers Magento_Di_Generator_EntityAbstract::_addError
     * @covers Magento_Di_Generator_EntityAbstract::_validateData
     * @covers Magento_Di_Generator_EntityAbstract::_getClassDocBlock
     * @covers Magento_Di_Generator_EntityAbstract::_getGeneratedCode
     * @covers Magento_Di_Generator_EntityAbstract::_fixCodeStyle
     */
    public function testGenerate($arguments, $errors = array())
    {
        $abstractGetters = array('_getClassProperties', '_getClassMethods');
        $this->_model = $this->getMockForAbstractClass(
            'Magento_Di_Generator_EntityAbstract', $arguments, '', true, true, true, $abstractGetters
        );
        // we need to mock abstract methods to set correct return value type
        foreach ($abstractGetters as $methodName) {
            $this->_model->expects($this->any())
                ->method($methodName)
                ->will($this->returnValue(array()));
        }

        $result = $this->_model->generate();
        if ($errors) {
            $this->assertFalse($result);
            $this->assertEquals($errors, $this->_model->getErrors());
        } else {
            $this->assertTrue($result);
            $this->assertEmpty($this->_model->getErrors());
        }
    }

    /**
     * Prepares mocks for validation verification
     *
     * @param bool $classExistsFirst
     * @param bool $classExistsSecond
     * @param bool $makeGeneration
     * @param bool $makeResultFile
     * @param bool $fileExists
     * @return array
     */
    protected function _prepareMocksForValidateData(
        $classExistsFirst = true,
        $classExistsSecond = false,
        $makeGeneration = true,
        $makeResultFile = true,
        $fileExists = false
    ) {
        $ioObject = $this->getMock('Magento_Di_Generator_Io',
            array(
                'getResultFileName',
                'makeGenerationDirectory',
                'makeResultFileDirectory',
                'fileExists',
                'getGenerationDirectory',
                'getResultFileDirectory',
                'writeResultFile'
            ),
            array(), '', false
        );
        $autoloader = $this->getMock('Magento_Autoload', array('classExists'), array(), '', false);

        $ioObject->expects($this->any())
            ->method('getResultFileName')
            ->with(self::RESULT_CLASS)
            ->will($this->returnValue(self::RESULT_FILE));
        $ioObject->expects($this->any())
            ->method('getGenerationDirectory')
            ->will($this->returnValue(self::GENERATION_DIRECTORY));
        $ioObject->expects($this->any())
            ->method('getResultFileDirectory')
            ->will($this->returnValue(self::RESULT_DIRECTORY));

        $autoloader->expects($this->at(0))
            ->method('classExists')
            ->with(self::SOURCE_CLASS)
            ->will($this->returnValue($classExistsFirst));
        if ($classExistsFirst) {
            $autoloader->expects($this->at(1))
                ->method('classExists')
                ->with(self::RESULT_CLASS)
                ->will($this->returnValue($classExistsSecond));
        }

        $expectedInvocations = 1;
        if ($classExistsFirst) {
            $expectedInvocations = 2;
        }
        $autoloader->expects($this->exactly($expectedInvocations))
            ->method('classExists');

        $expectedInvocations = 1;
        if ($classExistsSecond) {
            $expectedInvocations = 0;
        }
        $ioObject->expects($this->exactly($expectedInvocations))
            ->method('makeGenerationDirectory')
            ->will($this->returnValue($makeGeneration));

        $expectedInvocations = 0;
        if ($makeGeneration) {
            $expectedInvocations = 1;
        }
        $ioObject->expects($this->exactly($expectedInvocations))
            ->method('makeResultFileDirectory')
            ->with(self::RESULT_CLASS)
            ->will($this->returnValue($makeResultFile));

        $expectedInvocations = 0;
        if ($makeResultFile) {
            $expectedInvocations = 1;
        }
        $ioObject->expects($this->exactly($expectedInvocations))
            ->method('fileExists')
            ->with(self::RESULT_FILE)
            ->will($this->returnValue($fileExists));

        return array(
            'source_class'   => self::SOURCE_CLASS,
            'result_class'   => self::RESULT_CLASS,
            'io_object'      => $ioObject,
            'code_generator' => null,
            'autoloader'     => $autoloader
        );
    }

    /**
     * Prepares mocks for code generation test
     *
     * @param bool $isValid
     * @return array
     */
    protected function _prepareMocksForGenerateCode($isValid)
    {
        $mocks = $this->_prepareMocksForValidateData();

        $codeGenerator = $this->getMock('Magento_Di_Generator_CodeGenerator_Zend',
            array('setName', 'setProperties', 'setMethods', 'setDocblock', 'generate'), array(), '', false
        );
        $codeGenerator->expects($this->once())
            ->method('setName')
            ->with(self::RESULT_CLASS)
            ->will($this->returnSelf());
        $codeGenerator->expects($this->once())
            ->method('setProperties')
            ->will($this->returnSelf());
        $codeGenerator->expects($this->once())
            ->method('setMethods')
            ->will($this->returnSelf());
        $codeGenerator->expects($this->once())
            ->method('setDocblock')
            ->with($this->isType('array'))
            ->will($this->returnSelf());

        $codeGenerator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue($isValid ? self::SOURCE_CODE : null));

        /** @var $ioObject PHPUnit_Framework_MockObject_MockObject */
        $ioObject = $mocks['io_object'];
        $ioObject->expects($isValid ? $this->once() : $this->never())
            ->method('writeResultFile')
            ->with(self::RESULT_FILE, self::RESULT_CODE);

        return array(
            'source_class'   => $mocks['source_class'],
            'result_class'   => $mocks['result_class'],
            'io_object'      => $ioObject,
            'code_generator' => $codeGenerator,
            'autoloader'     => $mocks['autoloader'],
        );
    }

    public function testSetSourceClassName()
    {
        $this->assertAttributeEmpty('_sourceClassName', $this->_model);
        $this->_model->setSourceClassName(self::SOURCE_CLASS);
        $this->assertAttributeEquals(self::SOURCE_CLASS, '_sourceClassName', $this->_model);
    }

    public function testSetResultClassName()
    {
        $this->assertAttributeEmpty('_resultClassName', $this->_model);
        $this->_model->setResultClassName(self::RESULT_CLASS);
        $this->assertAttributeEquals(self::RESULT_CLASS, '_resultClassName', $this->_model);
    }
}
