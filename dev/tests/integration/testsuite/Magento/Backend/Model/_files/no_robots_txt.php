<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
use Magento\Framework\App\Filesystem\DirectoryList;

/** @var \Magento\Framework\Filesystem\Directory\Write $rootDirectory */
$rootDirectory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\Filesystem'
)->getDirectoryWrite(
    DirectoryList::ROOT
);
if ($rootDirectory->isExist('robots.txt')) {
    $rootDirectory->delete('robots.txt');
}
