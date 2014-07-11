<?php
/**
 * A version setter tool
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Composer\Package;

require __DIR__ . '/Package/Reader.php';
require __DIR__ . '/Package/Package.php';
require __DIR__ . '/Package/Collection.php';

$usage = "Usage: php -f version.php -- --version=2.1.3 [--dependent=<exact|wildcard>] [--dir=/path/to/work/dir]
--version - set the specified version value to all the components. Format: 'x.y.z' or 'x.y.z-stability.n'
--dependent - in all the dependent components, set a version of depenency
  exact - set exactly the same version as specified
  wildcard - use the specified version, but replace last number via a wildcard - e.g. 1.2.*
--dir - use specified path as the working directory
";

$opt = getopt('', ['version:', 'dependent::', 'dir::']);
try {
    if (!isset($opt['version'])) {
        throw new \InvalidArgumentException('Version number must be specified.');
    }
    if (!preg_match('/^\d+\.\d+\.\d+(\-(?:dev|alpha|beta|rc)\.\d+)?$/', $opt['version'], $matches)) {
        throw new \InvalidArgumentException('Wrong format of version number');
    }
    $collection = new Collection(isset($opt['dependent']) ? $opt['dependent'] : false);
    if (isset($opt['dir'])) {
        if (!is_dir($opt['dir'])) {
            throw new \InvalidArgumentException("The specified directory doesn't exist");
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
