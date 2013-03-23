<?php
/**
 * A command line tool that pre-populates static view files into public directory.
 * In the production mode paths and URLs are to be composed without the filesystem lookup.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../app/bootstrap.php';
Magento_Autoload_IncludePath::addIncludePath(__DIR__);

define('SYNOPSIS', <<<USAGE
Usage: php -f generator.php -- [--source <dir>] [--destination <dir>] [--dry-run]
       php -f generator.php -- --help

  --source <dir>      Root directory to start search of static view files from.
                      If omitted, the application root directory is used.

  --destination <dir> Directory to copy files to.
                      If omitted, public location of static view files is used.

  --dry-run           Do not create directories and files in a destination path.

  --help              Print this usage information.

USAGE
);

$options = getopt('', array('help', 'dry-run', 'source:', 'destination:'));
if (isset($options['help'])) {
    echo SYNOPSIS;
    exit(0);
}

echo "Deploying...\n";
try {
    $config = new Generator_Config(BP, $options);

    $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
    $dirs = new Mage_Core_Model_Dir($filesystem, $config->getSourceDir());
    $objectManager = new Magento_ObjectManager_ObjectManager();

    $themes = new Mage_Core_Model_Theme_Collection($filesystem, $objectManager, $dirs);
    $themes->setItemObjectClass('Generator_ThemeLight');
    $themes->addDefaultPattern('*');

    $generator = new Generator_CopyRule($filesystem, $themes, new Mage_Core_Model_Design_Fallback_List_View($dirs));
    $copyRules = $generator->getCopyRules();

    $deployment = new Generator_ThemeDeployment(
        $config->getDestinationDir(),
        __DIR__ . '/config/permitted.php',
        __DIR__ . '/config/forbidden.php',
        $config->isDryRun()
    );
    $deployment->run($copyRules);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    exit(1);
}
echo "Completed successfully.";
