<?php
/**
 * Magento product edition maker script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define('USAGE', 'php -f edition.php -- --dir="<working_directory>" --edition="<ce|ee>" [--build] [--additional="<dev_build_ce.txt>"]' . PHP_EOL);
try {
    $options = getopt('', array('dir:', 'edition:', 'build', 'additional:'));
    if (!isset($options['dir']) || !isset($options['edition'])) {
        throw new Exception(USAGE);
    }

    $basePath = realpath($options['dir']);
    require $basePath . '/app/autoload.php';
    Magento_Autoload_IncludePath::addIncludePath(
        array(
            realpath($basePath . '/dev/build/publication/edition/'),
            realpath($basePath . '/lib/'),
        )
    );

    /** @var $configurator ConfiguratorInterface */
    $configurator = null;

    $lists = array('common.txt');
    $isBuild = isset($options['build']);
    if (!$isBuild) {
        $lists[] = 'dev_build.txt';
    } elseif (isset($options['additional'])) {
        $lists[] = $options['additional'];
    }
    switch ($options['edition']) {
        case 'ce':
            $lists[] = 'ee.txt';
            $configurator = new CommunityConfigurator();
            break;
        case 'ee':
            $configurator = new EnterpriseConfigurator($basePath, new Magento_Io_File());
            break;
        default:
            throw new Exception("Specified edition '{$options['edition']}' is not implemented.");
    }

    //step #1: configure installation
    $configurator->configure();

    //step #2: remove files that not belong to edition
    $command = 'php -f ' . __DIR__ . '/../extruder.php -- -v -w ' . escapeshellarg($basePath);
    foreach ($lists as $list) {
        $command .= ' -l ' . escapeshellarg(__DIR__ . '/extruder/' . $list);
    }

    echo $command . PHP_EOL;
    passthru($command, $exitCode);
    if ($exitCode) {
        throw new Exception('Extruder execution failed');
    }

} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
