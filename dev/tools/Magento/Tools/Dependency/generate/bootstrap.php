<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

require_once __DIR__ . '/../../../../../../app/autoload.php';
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
    array(BP . '/dev/tests/static/framework', BP . '/dev/tools')
);
