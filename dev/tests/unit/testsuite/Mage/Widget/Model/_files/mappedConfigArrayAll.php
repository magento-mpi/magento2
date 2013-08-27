<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$array1File = __DIR__ . DIRECTORY_SEPARATOR . 'mappedConfigArray1.php';
$array1 = include $array1File;
$array2File = __DIR__ . DIRECTORY_SEPARATOR . 'mappedConfigArray2.php';
$array2 = include $array2File;
return array(
    'cms_page_link' => $array1,
    'magento_giftregistry_search' => $array2
);