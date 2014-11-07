<?php
/**
 * A tool for creating root composer.json files
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Composer\Package;

use \Magento\Tools\Composer\Helper\ReplaceFilter;
use Magento\Tools\Composer\Helper\VersionCalculator;

require __DIR__ . '/../../../bootstrap.php';

$skeletonOption = 'skeleton';
$productOption = 'product';

define(
    'USAGE',
    "Usage: php -f create-root.php --"
    . " [--type=<$skeletonOption|$productOption>] [--wildcard] [--source-dir=<path>] " .
    "[--target-file=<path>] [--set=<option:value>]
    --type=$skeletonOption|$productOption - render the result as a project skeleton or as a product.
        --type=$skeletonOption render the result as a project skeleton
        --type=$productOption render the result as a product
    --wildcard - whether to set 'require' versions to wildcard
    --source-dir=/path/to/magento/dir - path to a Magento root directory. By default will use current working copy
        this directory must contain a root composer.json which is going to be used as template.
    --target-file=/path/to/composer.json - render output to the specified file. If not specified, render into STDOUT.
    --set='path->to->node:value' - set a value to the specified node. Use colon to separate node path and value.
        Overrides anything that was before in the template or in default values.
        May be used multiple times to specify multiple values. For example:
        --set=name:vendor/package --set=extra->branch-alias->dev-master:2.0-dev\n"
);
$opt = getopt('', ['type::', 'wildcard', 'source-dir::', 'target-file::', 'set::']);

try {
    // read the initial root composer.json
    if (isset($opt['source-dir'])) {
        $source = $opt['source-dir'];
        assertArgument(!empty($source), 'The value for source directory must not be empty.');
    } else {
        $source = realpath(BP);
    }
    assertLogical(is_dir($source), "The source directory doesn't exist: {$source}");
    $sourceComposer = $source . '/composer.json';
    assertLogical(is_file($sourceComposer), "The source composer.json file doesn't exist: {$sourceComposer}");
    $package = new Package(json_decode(file_get_contents($sourceComposer)), $sourceComposer);

    // set defaults
    $defaults = [
        'name' => 'magento/project-community-edition',
        'description' => 'Magento project (Community Edition)',
        'license' => ['OSL-3.0', 'AFL-3.0'],
    ];
    $skeletonDefaults = [
        'name' => 'magento/skeleton-community-edition',
        'description' => 'Magento 2 Skeleton',
        'type' => 'magento2-component',
        'version' => $package->get('version'),
        'require' => [
            'magento/magento-composer-installer' => '*',
        ]
    ];
    $replaceFilter = new ReplaceFilter($source);
    $useWildcard = isset($opt['wildcard']);
    $skeletonVersion = VersionCalculator::calculateVersionValue(
        $package->get('version'),
        'self.version',
        $useWildcard
    );
    $skeletonName = $skeletonDefaults['name'];
    $productDefaults = [
        'name' => 'magento/product-community-edition',
        'description' => 'eCommerce Platform for Growth (Community Edition)',
        'type' => 'project',
        'version' => $package->get('version'),
        'repositories' => [
            [
                'type' => 'composer',
                'url' => 'http://packages.magento.com/'
            ]
        ],
        'require' => [
            $skeletonName => $skeletonVersion
        ],
    ];

    $opt['type'] = isset($opt['type']) ? $opt['type'] : 'default';
    switch ($opt['type']) {
        case $skeletonOption:
            $defaults = array_merge($defaults, $skeletonDefaults);
            $targetPackage = createSkeleton($package, $defaults, $source);
            break;
        case $productOption:
            $defaults = array_merge($defaults, $productDefaults);
            $targetPackage = createProduct($package, $defaults, $source, $useWildcard);
            break;
        default:
            $targetPackage = createDefault($package, $defaults, $source);
    }

    // override with "set" option
    if (isset($opt['set'])) {
        foreach ((array)$opt['set'] as $row) {
            assertLogical(preg_match('/^(.*?):(.+)$/', $row, $matches), "Unable to parse 'set' value: {$row}");
            list(, $key, $value) = $matches;
            $targetPackage->set($key, $value);
        }
    }

    // output
    $output = $targetPackage->getJson();
    if (isset($opt['target-file'])) {
        $file = $opt['target-file'];
        assertArgument(!empty($file), "Target file name must not be empty.");
        assertLogical(file_put_contents($file, $output), "Unable to record output to the file: {$file}");
        echo "Output has been recorded to: {$file}\n";
    } else {
        echo $output;
    }
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    echo USAGE;
    exit(1);
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
exit(0);

/**
 * Create default package
 *
 * @param Package $package
 * @param array $defaults
 * @param string $source
 * @return Package
 */
function createDefault($package, $defaults, $source)
{
    // defaults
    foreach ($defaults as $key => $value) {
        $package->set($key, $value);
    }

    // filter the "replace" elements
    $replaceFilter = new ReplaceFilter($source);
    $replaceFilter->removeMissing($package);

    return $package;
}

/**
 * Create skeleton package
 *
 * @param Package $package
 * @param array $defaults
 * @param string $source
 * @return Package
 */
function createSkeleton($package, $defaults, $source)
{
    $targetPackage = new Package(new \StdClass(), null);

    // defaults
    foreach ($defaults as $key => $value) {
        $targetPackage->set($key, $value);
    }

    // filter the "replace" elements
    $replaceFilter = new ReplaceFilter($source);
    $replaceFilter->removeMagentoComponentsFromReplace($package);
    $targetPackage->set('replace', $package->get('replace'));
    $targetPackage->set('extra->component_paths', $package->get('extra->component_paths'));

    // marshaling mapping (for skeleton)
    $reader = new Reader($source);
    $targetPackage->set('extra->map', $reader->getRootMappingPatterns());
    $targetPackage->set('autoload-dev', $package->get('autoload-dev'));

    return $targetPackage;
}

/**
 * Create product package
 *
 * @param Package $package
 * @param array $defaults
 * @param string source
 * @param bool $useWildcard
 * @return Package
 */
function createProduct($package, $defaults, $source, $useWildcard)
{
    $targetPackage = new Package(new \StdClass(), null);

    // defaults
    foreach ($defaults as $key => $value) {
        $targetPackage->set($key, $value);
    }

    // filter the "replace" elements
    $replaceFilter = new ReplaceFilter($source);
    $replaceFilter->removeMissing($package);
    $replaceFilter->moveMagentoComponentsToRequire($package, $useWildcard);
    foreach ($targetPackage->get('require') as $key => $value) {
        $package->set("require->{$key}", $value);
    }
    $targetPackage->set('require', $package->get('require'));

    return $targetPackage;
}

/**
 * Assert a condition and throw an \InvalidArgumentException if false
 *
 * @param bool $condition
 * @param string $error
 * @return void
 * @throws \InvalidArgumentException
 */
function assertArgument($condition, $error)
{
    if (!$condition) {
        throw new \InvalidArgumentException($error);
    }
}

/**
 * Assert a condition and throw an \Logic if false
 *
 * @param bool $condition
 * @param string $error
 * @return void
 * @throws \LogicException
 */
function assertLogical($condition, $error)
{
    if (!$condition) {
        throw new \LogicException($error);
    }
}
