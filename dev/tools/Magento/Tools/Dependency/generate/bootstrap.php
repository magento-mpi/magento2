<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

define('BP', realpath(__DIR__ . '/../../../../../../') . '/');

require_once BP . 'app/autoload.php';
\Magento\Autoload\IncludePath::addIncludePath([
    BP . 'dev/tests/static/framework',
    BP . 'dev/tools',
    BP . '/lib',
]);
