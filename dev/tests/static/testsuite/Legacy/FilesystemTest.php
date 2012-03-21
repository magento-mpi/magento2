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
        $this->assertFileNotExists(Utility_Files::init()->getPathToSource() . DIRECTORY_SEPARATOR . $path);
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
            array('Dissolved into different modules\' view/frontend' => 'app/design/frontend/base'),
        );
    }

    public function testObsoleteDirectories()
    {
        $area    = '*';
        $package = '*';
        $theme   = '*';
        $root = Utility_Files::init()->getPathToSource();
        $dirs = glob("{$root}/app/design/{$area}/{$package}/{$theme}/template", GLOB_ONLYDIR);
        $msg = array();
        if ($dirs) {
            $msg[] = 'Theme "template" directories are obsolete. Relocate files as follows:';
            foreach ($dirs as $dir) {
                $msg[] = str_replace($root, '',
                    "{$dir} => " . realpath($dir . '/..') . '/Namespace_Module/*'
                );
            }
        }

        $dirs = glob("{$root}/app/design/{$area}/{$package}/{$theme}/layout", GLOB_ONLYDIR);
        if ($dirs) {
            $msg[] = 'Theme "layout" directories are obsolete. Relocate layout files into the root of theme directory.';
            $msg = array_merge($msg, $dirs);
        }

        if ($msg) {
            $this->fail(implode(PHP_EOL, $msg));
        }
    }
}
