<?php
require_once(__DIR__ . '/_files/ClassesForContextAggregationTest.php');
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class ContextAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Code\Validator\ContextAggregation
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $this->_model = new \Magento\Code\Validator\ContextAggregation();
        $this->_fixturePath  = realpath(__DIR__) . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR . 'ClassesForContextAggregationTest.php';
    }

    public function testClassArgumentAlreadyInjectedIntoContext()
    {
        $message = 'Incorrect dependency in class ClassArgumentAlreadyInjectedIntoContext in '
            . $this->_fixturePath . PHP_EOL . '\ClassA already exists in context object';

        $this->setExpectedException('\Magento\Code\ValidationException', $message);
        $this->_model->validate('ClassArgumentAlreadyInjectedIntoContext');
    }

    public function testClassArgumentWithInterfaceImplementation()
    {
        $this->assertTrue($this->_model->validate('ClassArgumentWithInterfaceImplementation'));
    }

    public function testClassArgumentWithInterface()
    {
        $this->assertTrue($this->_model->validate('ClassArgumentWithInterface'));
    }

    public function testClassArgumentWithAlreadyInjectedInterface()
    {
        $message = 'Incorrect dependency in class ClassArgumentWithAlreadyInjectedInterface in '
            . $this->_fixturePath . PHP_EOL . '\\InterfaceA already exists in context object';

        $this->setExpectedException('\Magento\Code\ValidationException', $message);
        $this->_model->validate('ClassArgumentWithAlreadyInjectedInterface');
    }
}
