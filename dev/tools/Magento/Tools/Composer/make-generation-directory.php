<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

$root = realpath(__DIR__ . '/../../../../../');
$generationDir = '/var/generation/';
if (!file_exists($root . $generationDir)) {
    mkdir($root . $generationDir);
}
