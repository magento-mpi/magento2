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

class Legacy_FilesystemEnterpriseTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directories may re-appear again during merging, therefore ensure they were properly relocated
     *
     * @param string $path
     * @dataProvider relocationsDataProvider
     */
    public function testRelocations($path)
    {
        //$this->markTestSkipped('Skipping test until all references to Enterprise have been removed');
        $this->assertFileNotExists(Utility_Files::init()->getPathToSource() . DIRECTORY_SEPARATOR . $path);
    }

    /**
     * @return array
     */
    public function relocationsDataProvider()
    {
        return array(
            'Relocated to Magento' => array('app/code/core/Enterprise'),
        );
    }
}
