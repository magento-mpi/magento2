<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Autoload;

use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Autoload\AutoloaderRegistry;
use \Magento\Framework\Filesystem\FileResolver;

/**
 * Utility class for populating an autoloader with application-specific information for PSR-0 and PSR-4 mappings
 * and include-path contents
 */
class Populator
{

    /**
     * @param AutoloaderInterface $registry
     * @param DirectoryList $dirList
     * @return void
     */
    public static function populateMappings(AutoloaderInterface $autoloader, DirectoryList $dirList)
    {
        $modulesDir = $dirList->getPath(DirectoryList::MODULES);
        $generationDir = $dirList->getPath(DirectoryList::GENERATION);
        $frameworkDir = $dirList->getPath(DirectoryList::LIB_INTERNAL);

        $autoloader->addPsr4('Magento\\', [$modulesDir . '/Magento/', $generationDir . '/Magento/'], true);
        $autoloader->addPsr4('Zend\\Soap\\', $modulesDir . '/Zend/Soap/', true);
        $autoloader->addPsr4('Zend\\', $frameworkDir . '/Zend/', true);

        $autoloader->addPsr0('Apache_', $frameworkDir, true);
        $autoloader->addPsr0('Cm_', $frameworkDir, true);
        $autoloader->addPsr0('Credis_', $frameworkDir, true);
        $autoloader->addPsr0('Less_', $frameworkDir, true);
        $autoloader->addPsr0('Symfony\\', $frameworkDir, true);
        $autoloader->addPsr0('Zend_Date', $modulesDir, true);
        $autoloader->addPsr0('Zend_Mime', $modulesDir, true);
        $autoloader->addPsr0('Zend_', $frameworkDir, true);
        $autoloader->addPsr0('Zend\\', $frameworkDir, true);

        /** Required for Zend functionality */
        FileResolver::addIncludePath($frameworkDir);

        /** Required for code generation to occur */
        FileResolver::addIncludePath($generationDir);
    }
}
