<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Background image renderer test
 */
class Magento_DesignEditor_Model_Editor_QuickStyles_Renderer_BackgroundImageTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @cover Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage::toCss
     * @dataProvider backgroundImageData
     */
    public function testToCss($expectedResult, $data)
    {
        /** @var $rendererModel Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage */
        $rendererModel = $this->getMock(
            'Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage', null, array(), '', false
        );

        $this->assertEquals($expectedResult, $rendererModel->toCss($data));
    }

    /**
     * @cover Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage::toCss
     * @dataProvider backgroundImageDataClearDefault
     */
    public function testToCssClearDefault($expectedResult, $data)
    {
        /** @var $rendererModel Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage */
        $rendererModel = $this->getMock(
            'Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage', null, array(), '', false
        );

        $this->assertEquals($expectedResult, $rendererModel->toCss($data));
    }

    /**
     * @return array
     */
    public function backgroundImageData()
    {
        return array(array(
            'expected_result' => ".header { background-image: url('path/image.gif'); }",
            'data'            => array(
                'type'      => 'image-uploader',
                'default'   => 'bg.gif',
                'selector'  => '.header',
                'attribute' => 'background-image',
                'value'     => 'path/image.gif',
            ),
        ));
    }

    /**
     * @return array
     */
    public function backgroundImageDataClearDefault()
    {
        return array(array(
            'expected_result' => ".header { background-image: none; }",
            'data'            => array(
                'type'      => 'image-uploader',
                'default'   => 'bg.gif',
                'selector'  => '.header',
                'attribute' => 'background-image',
                'value'     => '',
            ),
        ));
    }
}
