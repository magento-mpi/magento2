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
    $configFile = __DIR__ . "/dev/build/saas_qa/tenants/local.{$tenantId}.xml";
    if (!$configFile || !file_exists($configFile) || !is_readable($configFile)) {
        throw new Exception("Tenant file '{$configFile}' cannot be found.");
    }
    $localXml = file_get_contents($configFile);
} catch (Exception $e) {
    header('Content-Type: text/plain;charset=UTF-8');
    header('HTTP/1.0 404 Not Found');
    echo $e->getMessage();
    exit(1);
}

// Determine application configuration string for a tenant
$appConfig = array(
    'custom_local_config' => $localXml,
    'app_uris' => array(
        'media' => "pub/media.{$tenantId}",
    ),
    'app_dirs' => array(
        'media' => __DIR__ . "/pub/media.{$tenantId}",
        'var'   => __DIR__ . "/var.{$tenantId}",
    ),
);

$entryPoint = new Mage_Core_Model_EntryPoint_Http(__DIR__, $appConfig);
$entryPoint->processRequest();
