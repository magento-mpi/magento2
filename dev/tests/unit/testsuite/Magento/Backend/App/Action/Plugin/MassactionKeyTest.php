<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Action\Plugin;

class MassactionKeyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Backend\App\Action\Plugin\MassactionKey
     */
    protected $plugin;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->subjectMock = $this->getMock('Magento\Backend\App\AbstractAction', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->plugin = new \Magento\Backend\App\Action\Plugin\MassactionKey();
    }

    /**
     * @covers \Magento\Backend\App\Action\Plugin\MassactionKey::aroundDispatch
     *
     * @param $postData array|string
     * @param array $convertedData
     * @dataProvider aroundDispatchDataProvider
     */
    public function testAroundDispatchWhenMassactionPrepareKeyRequestExists($postData, $convertedData)
    {

        $this->requestMock ->expects($this->at(0))
            ->method('getPost')->with('massaction_prepare_key')->will($this->returnValue('key'));
        $this->requestMock->expects($this->at(1))->method('getPost')->with('key')->will($this->returnValue($postData));
        $this->requestMock->expects($this->once())->method('setPost')->with('key', $convertedData);
        $this->assertEquals('Expected',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock));
    }

    public function aroundDispatchDataProvider()
    {
        return array(
            'post_data_is_array' => array(
                array('key'),  array('key')
            ),
            'post_data_is_string' => array(
                'key, key_two', array('key', ' key_two')
            )
        );
    }

    /**
     * @covers \Magento\Backend\App\Action\Plugin\MassactionKey::aroundDispatch
     */
    public function testAroundDispatchWhenMassactionPrepareKeyRequestNotExists()
    {

        $this->requestMock ->expects($this->once())
            ->method('getPost')->with('massaction_prepare_key')->will($this->returnValue(false));
        $this->requestMock->expects($this->never())->method('setPost');
        $this->assertEquals('Expected',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock));
    }
}
