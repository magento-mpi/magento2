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

class Magento_Test_Legacy_FilesystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directories may re-appear again during merging, therefore ensure they were properly relocated
     *
     * @param string $path
     * @dataProvider relocationsDataProvider
     */
    public function testRelocations($path)
    {
        $this->assertFileNotExists(Magento_TestFramework_Utility_Files::init()->getPathToSource() . DIRECTORY_SEPARATOR . $path);
    }

    /**
     * @return array
     */
    public function relocationsDataProvider()
    {
        return array(
            'Relocated to pub/errors' => array('errors'),
            'Eliminated with Magento_Compiler' => array('includes'),
            'Relocated to pub/lib' => array('js'),
            'Relocated to pub/media' => array('media'),
            'Eliminated as not needed' => array('pkginfo'),
            'Dissolved into themes under app/design ' => array('skin'),
            'Dissolved into different modules\' view/frontend' => array('app/design/frontend/base'),
            'Dissolved into different modules\' view/email/*.html' => array('app/locale/en_US/template'),
            'The "core" code pool no longer exists. Use root namespace as specified in PSR-0 standard' => array('app/code/core'),
            'The "local" code pool no longer exists. Use root namespace as specified in PSR-0 standard' => array('app/code/local'),
            'The "community" code pool no longer exists. Use root namespace as specified in PSR-0 standard' => array('app/code/community'),
        );
    }

    public function testObsoleteDirectories()
    {
        $area    = '*';
        $theme   = '*';
        $root = Magento_TestFramework_Utility_Files::init()->getPathToSource();
        $dirs = glob("{$root}/app/design/{$area}/{$theme}/template", GLOB_ONLYDIR);
        $msg = array();
        if ($dirs) {
            $msg[] = 'Theme "template" directories are obsolete. Relocate files as follows:';
            foreach ($dirs as $dir) {
                $msg[] = str_replace($root, '',
                    "{$dir} => " . realpath($dir . '/..') . '/Namespace_Module/*'
                );
            }
        }

        $dirs = glob("{$root}/app/design/{$area}/{$theme}/layout", GLOB_ONLYDIR);
        if ($dirs) {
            $msg[] = 'Theme "layout" directories are obsolete. Relocate layout files into the root of theme directory.';
            $msg = array_merge($msg, $dirs);
        }

        if ($msg) {
            $this->fail(implode(PHP_EOL, $msg));
        }
    }
}
