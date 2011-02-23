#!/usr/bin/php
<?php

$header = '<a style="display: block; float: left; height: 36px; width: 420px;" href="http://www.magentocommerce.com" target="_top"></a>%s<br/><a href="%s" target="_top">%s</a>';

switch ($argv[2]) {
    case 'pe' :
        $header_mage = sprintf($header, 'Magento Professional Documentation', 'Varien/index.html', 'Varien Lib Documentation');
        $header_lib = sprintf($header, 'Varien Lib Documentation', '../index.html', 'Magento Professional Documentation');
    break;
    case 'ee' :
        $header_mage = sprintf($header, 'Magento Enterprise Documentation', 'Varien/index.html', 'Varien Lib Documentation');
        $header_lib = sprintf($header, 'Varien Lib Documentation', '../index.html', 'Magento Enterprise Documentation');
    break;
    default :
        $header_mage = sprintf($header, 'Magento Documentation', 'Varien/index.html', 'Varien Lib Documentation');
        $header_lib = sprintf($header, 'Varien Lib Documentation', '../index.html', 'Magento Documentation');
    break;
}

function fix_header($file, $header)
{
    $file_content = file_get_contents($file);
    $file_content = preg_replace('/(<div class="banner-title">)(.*?)(<\/div>)/is', '$1' . $header. '$3', $file_content);
    file_put_contents($file, $file_content);
}

fix_header($argv[1] . '/packages.html', $header_mage);
fix_header($argv[1] . '/Varien/packages.html', $header_lib);

`rsync -aC phpdoc/media/ $argv[1]/media/`;
`rsync -aC phpdoc/media/ $argv[1]/Varien/media/`;

exit(0);
