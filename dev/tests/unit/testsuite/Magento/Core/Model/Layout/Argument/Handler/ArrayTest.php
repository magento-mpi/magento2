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
 * Test class for Magento_Core_Model_Layout_Argument_Handler_Array
 */
class Magento_Core_Model_Layout_Argument_Handler_ArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_Handler_Array
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_factoryMock = $this->getMock('Magento_Core_Model_Layout_Argument_HandlerFactory', array(), array(),
            '', false);
        $this->_model = new Magento_Core_Model_Layout_Argument_Handler_Array($this->_objectManagerMock,
            $this->_factoryMock
        );
    }

    /**
     * @param array $argument
     * @param array $expected
     * @dataProvider processDataProvider
     */
    public function testProcess($argument, $expected)
    {
        $getArgumentHandlerByTypeCallback = function ($type) use ($expected) {
            $handlerModel = $this->getMock(
                'Magento_Core_Model_Layout_Argument_HandlerInterface',
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
            ->will($this->returnCallback($getArgumentHandlerByTypeCallback));
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
     * @param Magento_Core_Model_Layout_Element $node
     * @param $expected array
     * @dataProvider parseDataProvider
     */
    public function testParse($node, $expected)
    {
        $getArgumentHandlerByTypeCallback = function ($type) {
            $handlerModel = $this->getMock(
                'Magento_Core_Model_Layout_Argument_HandlerInterface',
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
            ->will($this->returnCallback($getArgumentHandlerByTypeCallback));

        $result = $this->_model->parse(reset($node));
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        $layout = simplexml_load_file(
            __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'arguments.xml',
            'Magento_Core_Model_Layout_Element'
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
                    'updater' => array('Magento_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater'),
                    'value' => array(
                        'add' => 'array',
                    ),
                )
            ),
        );
    }
}
