<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\View;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Shell
     */
    protected $shell;

    /**
     * @var \Magento\Filesystem\Driver\File
     */
    protected $filesystem;

    /**
     * Temporary destination directory
     *
     * @var string
     */
    protected $tmpDir;

    protected function setUp()
    {
        $this->tmpDir = BP . '/var/static';
        $this->shell = new \Magento\Shell();
        $this->filesystem = new \Magento\Filesystem\Driver\File();
        $this->filesystem->createDirectory($this->tmpDir, 0777);
    }

    protected function tearDown()
    {
        if ($this->filesystem->isExists($this->tmpDir)) {
            $this->filesystem->deleteDirectory($this->tmpDir);
        }
    }

    /**
     * Test view generator
     */
    public function testViewGenerator()
    {
        try {
            $this->shell->execute(
                'php -f %s -- --source %s --destination %s',
                array(BP . '/dev/tools/Magento/Tools/View/generator.php', BP . '/app/design', $this->tmpDir)
            );
        } catch (\Magento\Exception $exception) {
            $this->fail($exception->getPrevious()->getMessage());
        }
    }
}
