#!/usr/bin/php
<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    publisher
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__FILE__) . '/Publisher.php';

define('USAGE', <<<USAGE
$>./publish.php --repository <path> [parameters]
    additional parameters:
    -v verbose output
USAGE
);

$shortOpts = 'v';
$longOpts  = array(
    'repository:',
);
$options = getopt($shortOpts, $longOpts);

if (!isset($options['repository'])) {
    print USAGE;
    exit(1);
}

$verbose = false;
if (isset($options['v'])) {
    $verbose = true;
}

try{
    $publisher = new Publisher();
    $publisher->setRepositoryPath($options['repository']);
    $publisher->setVerbose($verbose);
    $publisher->run();
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
exit(0);
