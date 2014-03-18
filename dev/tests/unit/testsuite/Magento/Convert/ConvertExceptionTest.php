<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Convert
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Convert;

class ConvertExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConvertException
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new ConvertException();
    }

    public function testContainer()
    {
        $object = new \stdClass();
        $this->model->setContainer($object);
        $this->assertEquals($object, $this->model->getContainer());
    }

    public function testLevel()
    {
        $level = 'test_level';
        $this->model->setLevel($level);
        $this->assertEquals($level, $this->model->getLevel());
    }

    public function testPosition()
    {
        $position = 2555;
        $this->model->setPosition($position);
        $this->assertEquals($position, $this->model->getPosition());
    }
}
