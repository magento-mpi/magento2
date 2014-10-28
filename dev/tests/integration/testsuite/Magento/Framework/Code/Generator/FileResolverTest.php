<?php
/**
 * Integration test for \Magento\Framework\Code\Generator\FileResolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code\Generator;

use Magento\TestFramework\Helper\Bootstrap;

class FileResolverTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Magento\Framework\Code\Generator\FileResolver
     */
    protected $model;

    public function setUp()
    {
        $this->model = Bootstrap::getObjectManager()->create('Magento\Framework\Code\Generator\FileResolver');
    }

    public function testAddIncludePathPrepend()
    {
        $originalIncludePath = get_include_path();
        set_include_path('/pre/existing/paths/');
        $firstPathToAdd = '/path/to/code/1/';
        $secondPathToAdd = '/path/to/code/2/';

        $this->model->addIncludePath($firstPathToAdd);
        $this->model->addIncludePath($secondPathToAdd);

        $postIncludePath = get_include_path();
        $this->assertStringStartsWith(
            $secondPathToAdd,
            $postIncludePath
        );

        set_include_path($originalIncludePath);
    }

    public function testAddIncludePathAppend()
    {
        $originalIncludePath = get_include_path();
        set_include_path('/pre/existing/paths/');
        $firstPathToAdd = '/path/to/code/1/';
        $secondPathToAdd = '/path/to/code/2/';

        $this->model->addIncludePath($firstPathToAdd, false);
        $this->model->addIncludePath($secondPathToAdd, false);

        $postIncludePath = get_include_path();
        $this->assertStringEndsWith(
            $secondPathToAdd,
            $postIncludePath
        );

        set_include_path($originalIncludePath);
    }

    public function testGetFile()
    {
        $originalIncludePath = get_include_path();
        set_include_path('/pre/existing/paths/');
        $includePath = realpath(__DIR__ . '/../_files/');
        $className = '\ClassToFind';

        $this->model->addIncludePath($includePath);
        $this->assertFileExists($this->model->getFile($className));

        set_include_path($originalIncludePath);
    }
}
