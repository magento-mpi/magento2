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
            'Relocated to pub/errors'                => array('errors'),
            'Eliminated with Mage_Compiler'          => array('includes'),
            'Relocated to pub/js'                    => array('js'),
            'Relocated to pub/media'                 => array('media'),
            'Eliminated as not needed'               => array('pkginfo'),
            'Dissolved into themes under app/design' => array('skin'),
        );
    }
}
