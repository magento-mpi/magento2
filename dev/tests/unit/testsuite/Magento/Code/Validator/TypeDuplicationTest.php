<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Validator;

require_once ('_files/ClassesForTypeDuplication.php');

class TypeDuplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Code\Validator\TypeDuplication
     */
    protected $_validator;

    /**
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $path = realpath(__DIR__) . '/' . '_files' . '/' . 'ClassesForTypeDuplication.php';
        $this->_fixturePath = str_replace('\\', '/', $path);
        $this->_validator = new \Magento\Code\Validator\TypeDuplication();
    }

    /**
     * @param $className
     * @dataProvider validClassesDataProvider
     */
    public function testValidClasses($className)
    {
        $this->assertTrue($this->_validator->validate($className));
    }

    public function validClassesDataProvider()
    {
        return array(
            'Duplicated interface injection' => array('\TypeDuplication\ValidClassWithTheSameInterfaceTypeArguments'),
            'Class with sub type arguments' => array('\TypeDuplication\ValidClassWithSubTypeArguments'),
            'Class with SuppressWarnings' => array('\TypeDuplication\ValidClassWithSuppressWarnings'),
        );
    }

    public function testInvalidClass()
    {
        $message = 'Argument type duplication in class TypeDuplication\InvalidClassWithDuplicatedTypes in '
            . $this->_fixturePath . PHP_EOL . 'Multiple type injection [\TypeDuplication\ArgumentBaseClass]';
        $this->setExpectedException('\Magento\Code\ValidationException', $message);
        $this->_validator->validate('\TypeDuplication\InvalidClassWithDuplicatedTypes');
    }
}

