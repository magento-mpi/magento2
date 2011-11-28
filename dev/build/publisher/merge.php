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

require dirname(__FILE__) . '/Merger.php';

define('USAGE', <<<USAGE
$>./merge.php --resource-repository <path> --goal-repository <path> --goal-repository-clone <path> [parameters]
    additional parameters:
    -v verbose output
USAGE
);

$shortOpts = 'v';
$longOpts  = array(
    'resource-repository:',
    'goal-repository:',
    'goal-repository-clone:',
);
$options = getopt($shortOpts, $longOpts);

if (!isset($options['goal-repository'])
        || !isset($options['goal-repository-clone'])
        || !isset($options['resource-repository'])) {
    print USAGE;
    exit(1);
}

$verbose = false;
if (isset($options['v'])) {
    $verbose = true;
}

try{
    $merger = new Merger();
    $merger->setGoalRepositoryPath($options['goal-repository']);
    $merger->setGoalRepositoryClonePath($options['goal-repository-clone']);
    $merger->setResourceRepositoryPath($options['resource-repository']);
    $merger->setVerbose($verbose);
    $merger->run();
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}

exit(0);
