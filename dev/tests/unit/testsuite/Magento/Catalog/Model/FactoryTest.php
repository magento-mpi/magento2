<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $model;

    /**
     * @var Factory
     */
    protected $factory;

    public function testCreate()
    {
        $this->assertInstanceOf('\Magento\Catalog\Model\Product\Option', $this->factory->create('model', []));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testExceptionCreate()
    {
        $this->factory->create('null', []);
    }

    protected function setUp()
    {
        $this->model = $this->getMock('Magento\Catalog\Model\Product\Option', [], [], '', false);

        $this->setObjectManager();

        $this->factory = new Factory($this->objectManager);
    }

    protected function setObjectManager()
    {
        $this->objectManager = $this->getMock('\Magento\Framework\ObjectManager', [], [], '', false);

        $this->objectManager
            ->expects($this->any())
            ->method('create')
            ->with($this->logicalOr($this->equalTo('model'), $this->equalTo('null')), $this->equalTo([]))
            ->will($this->returnCallback(function ($className, $data) {
                $returnValue = null;
                if ($className == 'model') {
                    $returnValue = $this->model;
                }
                return $returnValue;
            }));
    }
}