<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme css file model class
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_RendererTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider sampleData
     */
    public function testRender($expectedResult, $data)
    {
        /** @var $rendererModel Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer */
        $rendererModel = $this->getMock(
            'Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer', null, array(), '', false
        );

        $objectManager = $this->getMock(
            'Varien_Object', array('get', 'toCss'), array(), '', false
        );

        $objectManager->expects($this->exactly(4))
            ->method('get')
            ->will($this->returnValue($objectManager));

        $objectManager->expects($this->exactly(4))
            ->method('toCss')
            ->will($this->returnValue('css_string'));

        $property = new ReflectionProperty($rendererModel, '_quickStyleFactory');
        $property->setAccessible(true);
        $property->setValue($rendererModel, $objectManager);

        $this->assertEquals($expectedResult, $rendererModel->render($data));
    }

    /**
     * @return array
     */
    public function sampleData()
    {
        return array(array(
            'expected_result' => "css_string\ncss_string\ncss_string\ncss_string\n",
            'data'            => array(
                'header-background' => array(
                    'type'       => 'background',
                    'components' => array(
                        'header-background:color-picker' => array(
                            'type'      => 'color-picker',
                            'default'   => 'transparent',
                            'selector'  => '.header',
                            'attribute' => 'background-color',
                            'value'     => '#FFFFFF'
                        ),
                        'header-background:background-uploader' => array(
                            'type'       => 'background-uploader',
                            'components' => array(
                                'header-background:image-uploader' => array(
                                    'type'      => 'image-uploader',
                                    'default'   => 'bg.gif',
                                    'selector'  => '.header',
                                    'attribute' => 'background-image',
                                    'value'     => '../image.jpg'
                                ),
                                'header-background:tile' => array(
                                    'type'      => 'checkbox',
                                    'default'   => 'no-repeat',
                                    'options'   => array('no-repeat', 'repeat', 'repeat-x', 'repeat-y', 'inherit'),
                                    'selector'  => '.header',
                                    'attribute' => 'background-repeat',
                                    'value'     => 'checked'
                                ),
                            )
                        ),
                    )
                ),
                'menu-background' => array(
                    'type'      => 'color-picker',
                    'default'   => '#f8f8f8',
                    'selector'  => '.menu',
                    'attribute' => 'color',
                    'value'     => '#000000'
                ),
        )));
    }
}
