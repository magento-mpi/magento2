<?php
/**
 * "Access point" for multi-tenant build environment
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
try {
    if (!preg_match('/^([a-z0-9]+)\.tenant\..+$/', $_SERVER['HTTP_HOST'], $matches)) {
        throw new Exception('Tenant ID cannot be identified.');
    }
    $tenantId = $matches[1];
    $configFile = realpath(__DIR__ . "/../local.{$tenantId}.xml");
    if (!$configFile || !file_exists($configFile) || !is_readable($configFile)) {
        throw new Exception("Tenant file '{$configFile}' cannot be found.");
    }
    $localXml = file_get_contents($configFile);
} catch (Exception $e) {
    header('Content-Type: text/plain;charset=UTF-8');
    header('HTTP/1.0 400 Bad Request');
    echo $e;
    exit(1);
}

// Determine application configuration string for a tenant
$appConfigString = serialize(array(
    'MAGE_CONFIG_DATA' => $localXml,
    'app_dirs' => array(
        'media'  => "pub/media.{$tenantId}",
        'var'    => "var.{$tenantId}",
    ),
));
// Locate the application entry point
/** @var $appEntryPoint callable */
$appEntryPoint = require __DIR__ . '/saas.php';
// Delegate execution to the application entry point
$appEntryPoint($appConfigString);
