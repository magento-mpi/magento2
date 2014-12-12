<?php
/**
 * A version setter tool
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tools\Composer\Package;

require __DIR__ . '/../../../bootstrap.php';

$usage = "Usage: php -f version.php -- --version=2.1.3 [--dependent=<exact|wildcard>] [--dir=/path/to/work/dir]
--version - set the specified version value to all the components. Possible formats:
    x.y.z
    x.y.z-<alpha|beta|rc>n
--dependent - in all the dependent components, set a version of depenency
  exact - set exactly the same version as specified
  wildcard - use the specified version, but replace last number with a wildcard - e.g. 1.2.*
--dir - use specified path as the working directory
";

$opt = getopt('', ['version:', 'dependent::', 'dir::']);
try {
    if (!isset($opt['version'])) {
        throw new \InvalidArgumentException('Version number must be specified.');
    }
    Version::validate($opt['version']);
    $collection = new Collection(isset($opt['dependent']) ? $opt['dependent'] : false);
    if (isset($opt['dir'])) {
        if (!is_dir($opt['dir'])) {
            throw new \InvalidArgumentException("The specified directory doesn't exist: '{$opt['dir']}'");
        }
        $rootDir = $opt['dir'];
    } else {
        $rootDir = str_replace('\\', '/', realpath(__DIR__ . '/../../../../..'));
    }
    $reader = new Reader($rootDir);
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . "\n\n";
    echo $usage;
    exit(1);
}

try {
    foreach ($reader->readMagentoPackages() as $package) {
        $collection->add($package);
    }
    $rootPackage = $reader->readFromDir('');
    if ($rootPackage) {
        $collection->add($rootPackage);
    }
    $packages = $collection->getPackages();
    foreach (array_keys($packages) as $packageName) {
        $collection->setVersion($packageName, $opt['version']);
    }
    foreach ($packages as $package) {
        $file = $package->getFile();
        echo  $file . "\n";
        file_put_contents($file, $package->getJson());
    }
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
