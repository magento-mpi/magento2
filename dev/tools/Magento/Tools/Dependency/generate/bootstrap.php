<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

require_once __DIR__ . '/../../../../../../app/autoload.php';
\Magento\Framework\Code\Generator\FileResolver::addIncludePath(
    [BP . '/dev/tests/static/framework', BP . '/dev/tools']
);