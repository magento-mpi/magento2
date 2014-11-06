<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Setup\Mvc\Bootstrap\InitParamListener;

return [
    InitParamListener::BOOTSTRAP_PARAM => [
        Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => [DirectoryList::ROOT => [DirectoryList::PATH => BP]]
    ]
];
