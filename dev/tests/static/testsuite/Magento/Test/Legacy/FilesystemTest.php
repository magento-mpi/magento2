<?php
/**
 * Backwards-incompatible changes in file system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Legacy;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    public function testRelocations()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * Directories may re-appear again during merging, therefore ensure they were properly relocated
             *
             * @param string $path
             */
            function ($path) {
                $this->assertFileNotExists(
                    \Magento\TestFramework\Utility\Files::init()->getPathToSource() . '/' . $path
                );
            },
            $this->relocationsDataProvider()
        );
    }

    /**
     * @return array
     */
    public function relocationsDataProvider()
    {
        return array(
            'Relocated to pub/errors' => array('errors'),
            'Eliminated with Magento_Compiler' => array('includes'),
            'Eliminated with Magento_GoogleCheckout' => array('lib/googlecheckout'),
            'Relocated to lib/web' => array('js'),
            'Relocated to pub/media' => array('media'),
            'Eliminated as not needed' => array('pkginfo'),
            'Dissolved into themes under app/design ' => array('skin'),
            'Dissolved into different modules\' view/frontend' => array('app/design/frontend/base'),
            'Dissolved into different modules\' view/email/*.html' => array('app/locale/en_US/template'),
            'The "core" code pool no longer exists. Use root namespace as specified in PSR-0 standard' => array(
                'app/code/core'
            ),
            'The "local" code pool no longer exists. Use root namespace as specified in PSR-0 standard' => array(
                'app/code/local'
            ),
            'The "community" code pool no longer exists. Use root namespace as specified in PSR-0 standard' => array(
                'app/code/community'
            ),
            'Eliminated Magento/plushe theme' => ['app/design/frontend/Magento/plushe'],
        );
    }

    public function testObsoleteDirectories()
    {
        $area = '*';
        $theme = '*';
        $root = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $dirs = glob("{$root}/app/design/{$area}/{$theme}/template", GLOB_ONLYDIR);
        $msg = array();
        if ($dirs) {
            $msg[] = 'Theme "template" directories are obsolete. Relocate files as follows:';
            foreach ($dirs as $dir) {
                $msg[] = str_replace($root, '', "{$dir} => " . realpath($dir . '/..') . '/Namespace_Module/*');
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

    public function testObsoleteViewPaths()
    {
        $pathsToCheck = [
            'app/code/*/*/view/frontend/*'  => ['requirejs-config.js'],
            'app/code/*/*/view/adminhtml/*'  => ['requirejs-config.js'],
            'app/code/*/*/view/base/*'  => ['requirejs-config.js'],
            'app/design/*/*/*/*'     => ['requirejs-config.js', 'theme.xml'],
            'app/design/*/*/*/*_*/*' => ['requirejs-config.js'],
        ];
        $errors = [];
        foreach ($pathsToCheck as $path => $allowedFiles) {
            $foundFiles = glob(BP . '/' . $path);
            foreach ($foundFiles as $file) {
                if (is_dir($file) || in_array(basename($file), $allowedFiles)) {
                    continue;
                }
                $errors[] = "Wrong location of view file: '$file'. "
                    . "Please, put template files inside 'templates' sub-dir, "
                    . "static view files inside 'web' sub-dir and layout updates inside 'layout' sub-dir";
            }
        }
        if (!empty($errors)) {
            $this->fail(implode(PHP_EOL, $errors));
        }
    }
}
