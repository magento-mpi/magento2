#!/usr/bin/php
<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../app/autoload.php';
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(__DIR__ . '/../../lib/internal');

define(
'USAGE',
<<<USAGE
$>./extruder.php -w <working_dir> -l /path/to/list.txt [[-l /path/to/extra.txt] parameters]
    additional parameters:
    -w dir  directory with working copy to edit with the extruder
    -l      one or many files with lists that refer to files and directories to be deleted
    -v      additional verbosity in output

USAGE
);

echo 'Hello - it works!';
