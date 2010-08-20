#!/usr/bin/php
<?php

$workingDir = $argv[1];
$user = $argv[2];
$pass = $argv[3];
$rev = $argv[4];

$_classMap = array(
   'Mage/Core/Model/Mysql4/Abstract'               => 'Mage/Core/Model/Resource/Db/Abstract',
   'Mage/Core/Model/Mysql4/Collection/Abstract'    => 'Mage/Core/Model/Resource/Db/Collection/Abstract',
   'Mage/Catalog/Model/Resource/Eav/Mysql4'        => 'Mage/Catalog/Model/Resource',
   'Mage/Customer/Model/Entity/'                   => 'Mage/Customer/Model/Resource/',
   '/Model/Mysql4'                                 => '/Model/Resource',
);

exec(sprintf('svn st %s | grep ^C', $workingDir), $output, $exitCode);

foreach ($output as $line) {
    list($status, $file) = explode("       ", $line);
    $file = str_replace($workingDir . '/' , '', $file);
    $newFile = strtr($file, $_classMap);
    exec(sprintf('svn diff -c %s --username %s --password %s --no-auth-cache http://svn.magentocommerce.com/svn/magento/base/magento/trunk/%s | patch -l %s/%s', $rev, $user, $pass, $file, $workingDir, $newFile), $output2, $exitCode2);
    analyzeExitCode($exitCode2, $output2);
    exec(sprintf('svn revert --username %s --password %s --no-auth-cache %s/%s', $user, $pass, $workingDir, $file), $output2, $exitCode2);
    analyzeExitCode($exitCode2, $output2);
    exec(sprintf('rm %s/%s.*', $workingDir, $newFile));
}

function analyzeExitCode($exitCode, $output)
{
    if ($exitCode) {
        echo "Error: " . implode("\n", $output);
	exit($exitCode);
    }
}

exit(0);
