<?php
/**
 * Shortcut for the command line tool that pre-populates static view files for the production mode.
 * Interface is invariant, SaaS infrastructure relies on it when deploying a tenant on any Magento version.
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('SYNOPSIS', <<<USAGE
Usage: php -f generate_view_files.php -- --destination <dir>

  --destination <dir> Directory path to deploy static view files to

USAGE
);

$options = getopt('', array('destination:'));
if (empty($options['destination'])) {
    echo SYNOPSIS;
    exit(1);
}

$command = sprintf(
    'php -f %s -- --destination %s',
    escapeshellarg(dirname(__DIR__) . '/dev/tools/view/generator.php'),
    escapeshellarg($options['destination'])
);
passthru($command, $exitCode);
exit($exitCode);
