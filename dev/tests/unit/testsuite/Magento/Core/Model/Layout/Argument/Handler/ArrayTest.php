<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Layout\Argument\Handler\ArrayHandler
 */
namespace Magento\Core\Model\Layout\Argument\Handler;

class ArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\Handler\ArrayHandler
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento\Core\Model\Layout\Argument\HandlerFactory', array(), array(),
            '', false);
        $this->_model = new \Magento\Core\Model\Layout\Argument\Handler\ArrayHandler($this->_factoryMock);
    }

    /**
     * @param array $argument
     * @param array $expected
     * @dataProvider processDataProvider
     */
    public function testProcess($argument, $expected)
    {
        $getHandlerCallback = function ($type) use ($expected) {
            $handlerModel = $this->getMock(
                'Magento\Core\Model\Layout\Argument\HandlerInterface',
                array(),
                array(),
                '',
                false);
            $handlerModel->expects($this->once())->method('process')
                ->will($this->returnValue($expected[$type . 'Argument']));
            return $handlerModel;
        };

        $this->_factoryMock->expects($this->any())
            ->method('getArgumentHandlerByType')
            ->will($this->returnCallback($getHandlerCallback));
        $this->assertEquals($expected, $this->_model->process($argument));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return array(
            array(
                array(
                    'type' => 'array',
                    'value' => array(
                        'arrayArgument' => array(
                            'type' => 'array',
                            'value' => array(
                                'label' => array(
                                    'type' => 'string',
                                    'value' => array(
                                        'string' => 'CSV',
                                        'translate' => true
                                    )
                                )
                            )
                        ),
                        'urlArgument' => array(
                            'type' => 'url',
                            'value' => array(
                                'path' => '*/*/exportMsxml'
                            )
                        ),
                        'stringArgument' => array(
                            'type' => 'string',
                            'value' => array(
                                'value' => 'Excel XML',
                            )
                        )
                    ),
                ),
                array(
                    'arrayArgument' => array(
                        'label' => 'CSV'
                    ),
                    'urlArgument' => '*/*/exportMsxml',
                    'stringArgument' => 'Excel XML'
                )
            ),
        );
    }

    /**
     * @param \Magento\View\Layout\Element $node
     * @param $expected array
     * @dataProvider parseDataProvider
     */
    public function testParse($node, $expected)
    {
        $getHandlerCallback = function ($type) {
            $handlerModel = $this->getMock(
                'Magento\Core\Model\Layout\Argument\HandlerInterface',
                array(),
                array(),
                '',
                false);
            $handlerModel->expects($this->once())->method('parse')
                ->will($this->returnValue($type));
            return $handlerModel;
        };

        $this->_factoryMock->expects($this->any())
            ->method('getArgumentHandlerByType')
            ->will($this->returnCallback($getHandlerCallback));

        $result = $this->_model->parse(reset($node));
        if (isset($result['updaters'])) {
            $result['updaters'] = array_values($result['updaters']);
        }
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        $layout = simplexml_load_file(
            __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'arguments.xml',
            'Magento\View\Layout\Element'
        );

        return array(
            array(
                $layout->xpath('//argument[@name="testSimpleArray"]'),
                array(
                    'type' => 'array',
                    'value' => array(
                        'csv' => 'array',
                        'urlPath' => 'url',
                        'label' => 'string',
                    ),
                )
            ),
            array(
                $layout->xpath('//argument[@name="testArrayWithUpdater"]'),
                array(
                    'type' => 'array',
                    'updaters' => array('Magento\Sales\Model\Order\Grid\Massaction\ItemsUpdater'),
                    'value' => array(
                        'add' => 'array',
                    ),
                )
            ),
        );
    }
}
