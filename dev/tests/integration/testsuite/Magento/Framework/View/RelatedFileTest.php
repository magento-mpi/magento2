<?php
/**
 * {license_notice}
 *
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

class RelatedFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RelatedFile
     */
    protected $model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->get('Magento\Framework\View\RelatedFile');
    }

    /**
     * @dataProvider buildPathDataProvider
     */
    public function testBuildPath($arguments, $expected)
    {
        $path = $this->model->buildPath(
            $arguments['relatedFilePath'],
            $arguments['parentRelativePath'],
            $arguments['params']
        );
        $this->assertEquals($expected['path'], $path);
        $this->assertEquals($expected['params'], $arguments['params']);
    }

    /**
     * @return array
     */
    public function buildPathDataProvider()
    {
        return array(
            array(
                'arguments' => array(
                    'relatedFilePath' => '../directory/file.css',
                    'parentRelativePath' => 'css/source.css',
                    'params' => array('module' => false)
                ),
                'expected' => array('path' => 'directory/file.css', 'params' => array('module' => false))
            ),
            array(
                'arguments' => array(
                    'relatedFilePath' => '../some_dir/file.css',
                    'parentRelativePath' => 'css/source.css',
                    'params' => array('module' => 'Magento_Theme')
                ),
                'expected' => array('path' => 'some_dir/file.css', 'params' => array('module' => 'Magento_Theme'))
            ),
            array(
                'arguments' => array(
                    'relatedFilePath' => 'Magento_Theme::some_dir/file.css',
                    'parentRelativePath' => 'css/source.css',
                    'params' => array('module' => false)
                ),
                'expected' => array('path' => 'some_dir/file.css', 'params' => array('module' => 'Magento_Theme'))
            )
        );
    }
}
