<?php
/**
 * Backwards-incompatible changes in file system
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Legacy_FilesystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directories may re-appear again during merging, therefore ensure they were properly relocated
     *
     * @param string $path
     * @dataProvider relocationsDataProvider
     */
    public function testRelocations($path)
    {
        $this->assertFileNotExists(realpath(__DIR__ . '/../../../../..') . DIRECTORY_SEPARATOR . $path);
    }

    public function relocationsDataProvider()
    {
        return array(
            array('Relocated to pub/errors' => 'errors'),
            array('Eliminated with Mage_Compiler' => 'includes'),
            array('Relocated to pub/js' => 'js'),
            array('Relocated to pub/media' => 'media'),
            array('Eliminated as not needed' => 'pkginfo'),
            array('Dissolved into themes under app/design ' => 'skin'),
        );
    }
}
