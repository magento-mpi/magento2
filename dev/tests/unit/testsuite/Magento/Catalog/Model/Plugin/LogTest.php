<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Plugin;

class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Plugin\Log
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $compareItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $logResourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->logResourceMock = $this->getMock('Magento\Log\Model\Resource\Log', array(), array(), '', false);
        $this->compareItemMock = $this->getMock(
            'Magento\Catalog\Model\Product\Compare\Item', array(), array(), '', false
        );
        $this->subjectMock = $this->getMock('Magento\Log\Model\Resource\Log', array(), array(), '', false);
        $this->model = new \Magento\Catalog\Model\Plugin\Log($this->compareItemMock);
    }

    /**
     * @covers \Magento\Catalog\Model\Plugin\Log::afterClean
     */
    public function testAfterClean()
    {
        $this->compareItemMock->expects($this->once())
            ->method('clean');

        $this->assertEquals($this->logResourceMock,
            $this->model->afterClean($this->subjectMock, $this->logResourceMock));
    }
}
