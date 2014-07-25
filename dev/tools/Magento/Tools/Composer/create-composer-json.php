<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../bootstrap.php';

use \Magento\Tools\Composer\Package\Reader;
use \Magento\Tools\Composer\Package\Package;

/**
 * Composer Skeleton Creator Tool
 *
 * This tool creates skeleton composer.json file.
 */
try {
    $opt = new \Zend_Console_Getopt(
        array(
            'category|c=s' => 'Category of which packaging is done. Acceptable values: [ce|sk]',
            'dir|d=s' => 'Working directory. Default value ' . realpath(BP),
        )
    );
    $opt->parse();

    if ($opt->getOption('d')) {
        $workingDir = realpath($opt->getOption('d'));
    } else {
        $workingDir = realpath(BP);
    }

    if (!$workingDir || !is_dir($workingDir)) {
        throw new Exception($opt->getOption('d') . " must be a Magento code base.");
    }

    $logWriter = new \Zend_Log_Writer_Stream('php://output');
    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat('H:i:s');
    $logger->info('Working copy root directory: ' . $workingDir);

    $root = new Package(
        json_decode(file_get_contents(__DIR__ . '/etc/root_composer_template.json')),
        __DIR__  . '/composer.json'
    );
    $root->set('name', 'magento/community-edition');
    $root->set('description', 'eCommerce Platform for Growth (Community Edition)');
    $root->set('license', array("OSL-3.0", "AFL-3.0"));

    $mainlineJson = json_decode(file_get_contents($workingDir  . '/composer.json'));
    $root->set('version', $mainlineJson->{'version'});
    $root->set('type', $mainlineJson->{'type'});
    //collecting hardcoded require list from root composer.json
    foreach ($mainlineJson->{'require'} as $key=>$value) {
        $root->set("require->$key", $value);
    }
    //collecting hardcoded require-dev list from root composer.json
    foreach ($mainlineJson->{'require-dev'} as $key=>$value) {
        $root->set("require-dev->$key", $value);
    }
    //collecting hardcoded autoload list from root composer.json
    foreach ($mainlineJson->{'autoload'} as $key=>$value) {
        $root->set("autoload->$key", $value);
    }
    //add third-party components as 'replace'
    $componentsPaths = $mainlineJson->{'extra'}->{'component_paths'};
    foreach ($mainlineJson->{'replace'} as $name=>$version) {
        if (!(strncmp($name, 'magento/', strlen('magento/')) === 0)) {
            foreach ($componentsPaths as $cname=>$path) {
                $found = false;
                if ($cname === $name) {
                    if (is_array($path)) {
                        foreach ($path as $onePath) {
                            if (file_exists($workingDir . $onePath)) {
                                $found = true;
                                break;
                            }
                        }
                    }
                } else {
                    if (file_exists($workingDir . $path)) {
                        $found = true;
                    }
                }
            }
            if ($found == true) {
                $root->set("replace->$name", $version);
                break;
            }
        }
    }

    $reader = new Reader($workingDir);
    $category = $opt->getOption('c');
    switch (strtolower($category)) {
        case 'ce':
            //adding magento components
            foreach ($reader->readMagentoPackages() as $package) {
                $root->set("replace->{$package->get('name')}", "self.version");
            }
            break;
        case 'sk':
            $root->set("require->magento/magento-composer-installer", "*");
            //adding magento components
            foreach ($reader->readMagentoPackages() as $package) {
                $root->set("require->{$package->get('name')}", $package->get('version'));
            }
            $root->set('extra->map', $reader->getRootMappingPatterns());
            break;
        default:
            throw new \Zend_Console_Getopt_Exception('Category value not acceptable. Acceptable values: [ml|ce|sk]');
    }

    $size = sizeof((array)$root->get('require'));
    $size += sizeof((array)$root->get('require-dev'));
    $logger->info("Total number of dependencies in the CE skeleton package: {$size}");
    file_put_contents($root->getFile(), $root->getJson());
    $logger->info("SUCCESS: created package at {$root->getFile()}");
} catch (\Zend_Console_Getopt_Exception $e) {
    echo $e->getMessage();
    exit(1);
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
