<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Product\Type;

/**
 * Class \Magento\ConfigurableProduct\Model\Product\Type\PluginTest
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $expected
     * @param array $data
     * @dataProvider afterGetOptionArrayDataProvider
     */
    public function testAfterGetOptionArray(array $expected, array $data)
    {
        $moduleManagerMock = $this->getMock(
            'Magento\Framework\Module\Manager', array('isOutputEnabled'), array(), '', false
        );
        $moduleManagerMock->expects($this->once())
            ->method('isOutputEnabled')
            ->with('Magento_ConfigurableProduct')
            ->will($this->returnValue($data['is_module_output_enabled']));

        $model = new \Magento\ConfigurableProduct\Model\Product\Type\Plugin($moduleManagerMock);
        $this->assertEquals(
            $expected,
            $model->afterGetOptionArray($data['subject'], $data['result'])
        );
    }

    /**
     * @return array
     */
    public function afterGetOptionArrayDataProvider()
    {
        $productTypeMock = $this->getMock('Magento\Catalog\Model\Product\Type', array(), array(), '', false);
        return array(
            array(
                array(
                    'configurable' => true,
                    'not_configurable' => true
                ),
                array(
                    'is_module_output_enabled' => true,
                    'subject' => $productTypeMock,
                    'result' => array(
                        'configurable' => true,
                        'not_configurable' => true
                    )
                )
            ),
            array(
                array(
                    'not_configurable' => true
                ),
                array(
                    'is_module_output_enabled' => false,
                    'subject' => $productTypeMock,
                    'result' => array(
                        'configurable' => true,
                        'not_configurable' => true
                    )
                )
            )
        );
    }
}
