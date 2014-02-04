<?php
/**
 * {license_notice}
 *
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class RelatedFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RelatedFile
     */
    protected $model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->get('Magento\View\RelatedFile');
    }

    /**
     * @dataProvider buildPathDataProvider
     */
    public function testBuildPath($arguments, $expected)
    {
        $path = $this->model->buildPath(
            $arguments['relatedFilePath'],
            $arguments['parentPath'],
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
        $themesPath = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Filesystem')->getPath(\Magento\App\Filesystem::THEMES_DIR);

        return array(
            array(
                'arguments' => array(
                    'relatedFilePath' => '../directory/file.css',
                    'parentPath' => '/root/app/design/frontend/magento_plushe/css/source.css',
                    'parentRelativePath' => 'css/source.css',
                    'params' => ['module' => false]
                ),
                'expected' => array(
                    'path' => 'css/../directory/file.css',
                    'params' => ['module' => false]
                )
            ),
            array(
                'arguments' => array(
                    'relatedFilePath' => '../some_dir/file.css',
                    'parentPath' => ($themesPath . '/frontend/magento_plushe/css/source.css'),
                    'parentRelativePath' => 'css/source.css',
                    'params' => ['module' => 'Magento_Theme']
                ),
                'expected' => array(
                    'path' => 'css/../some_dir/file.css',
                    'params' => ['module' => 'Magento_Theme']
                )
            ),
            array(
                'arguments' => array(
                    'relatedFilePath' => 'Magento_Theme::some_dir/file.css',
                    'parentPath' => ($themesPath . '/frontend/magento_plushe/css/source.css'),
                    'parentRelativePath' => 'css/source.css',
                    'params' => ['module' => false]
                ),
                'expected' => array(
                    'path' => 'some_dir/file.css',
                    'params' => ['module' => 'Magento_Theme']
                )
            )
        );
    }
}
