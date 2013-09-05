<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Generator_EntityTestAbstract extends PHPUnit_Framework_TestCase
{
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

    protected static $_expectedMethods = array();

    /**
     * @return \Magento\Code\Generator\Io|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getIoObjectMock()
    {
        $ioObjectMock = $this->getMock('Magento\Code\Generator\Io',
            array('getResultFileName', 'makeGenerationDirectory', 'makeResultFileDirectory', 'fileExists',
                'writeResultFile'
            ), array(), '', false
        );
        $ioObjectMock->expects($this->any())
            ->method('getResultFileName')
            ->will($this->returnValue(static::RESULT_FILE));
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
            ->with(static::RESULT_FILE, static::CODE);

        return $ioObjectMock;
    }

    /**
     * @return \Magento\Autoload\IncludePath|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAutoloaderMock()
    {
        $autoLoaderMock = $this->getMock('Magento\Autoload\IncludePath', array('getFile'), array(), '', false);
        $autoLoaderMock->staticExpects($this->at(0))
            ->method('getFile')
            ->with(static::SOURCE_CLASS)
            ->will($this->returnValue(true));
        $autoLoaderMock->staticExpects($this->at(1))
            ->method('getFile')
            ->with(static::RESULT_CLASS)
            ->will($this->returnValue(false));

        return $autoLoaderMock;
    }

    /**
     * @param array $methodNames
     * @return \Magento\Code\Generator\CodeGenerator\Zend|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCodeGeneratorMock(array $methodNames)
    {
        $codeGeneratorMock
            = $this->getMock('Magento\Code\Generator\CodeGenerator\Zend', $methodNames, array(), '', false);
        $codeGeneratorMock->expects($this->once())
            ->method('setName')
            ->with(static::RESULT_CLASS)
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('addProperties')
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('addMethods')
            ->with(static::$_expectedMethods)
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('setClassDocBlock')
            ->with($this->isType('array'))
            ->will($this->returnSelf());
        $codeGeneratorMock->expects($this->once())
            ->method('generate')
            ->will($this->returnValue(self::CODE));

        return $codeGeneratorMock;
    }
}
