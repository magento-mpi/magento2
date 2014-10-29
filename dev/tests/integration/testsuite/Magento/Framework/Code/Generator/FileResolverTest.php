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

class FileResolverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Path to add to include path
     */
    const FIRST_PATH = '/path/to/code/1/';

    /**
     * Path to add to include path
     */
    const SECOND_PATH = '/path/to/code/2/';

    /**
     * @var \Magento\Framework\Code\Generator\FileResolver
     */
    protected $model;

    /**
     * @var string original include-path variable
     */
    protected $originalPath;

    public function setUp()
    {
        $this->model = Bootstrap::getObjectManager()->create('Magento\Framework\Code\Generator\FileResolver');
        $this->originalPath = get_include_path();
        set_include_path('/pre/existing/paths/');
    }

    public function tearDown()
    {
        set_include_path($this->originalPath);
    }

    public function testAddIncludePathPrepend()
    {
        $this->model->addIncludePath(self::FIRST_PATH);
        $this->model->addIncludePath(self::SECOND_PATH);

        $postIncludePath = get_include_path();
        $this->assertStringStartsWith(
            self::SECOND_PATH,
            $postIncludePath
        );
    }

    public function testAddIncludePathAppend()
    {
        $this->model->addIncludePath(self::FIRST_PATH, false);
        $this->model->addIncludePath(self::SECOND_PATH, false);

        $postIncludePath = get_include_path();
        $this->assertStringEndsWith(
            self::SECOND_PATH,
            $postIncludePath
        );
    }

    public function testGetFile()
    {
        $includePath = realpath(__DIR__ . '/../_files/');
        $className = '\ClassToFind';

        $this->model->addIncludePath($includePath);
        $this->assertFileExists($this->model->getFile($className));
    }
}
