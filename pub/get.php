<?php
/**
 * Public media files entry point
 *
 * {license_notice}
 *
 * @category   Magento
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__DIR__) . '/app/bootstrap.php';

$mediaDirectory = null;
$allowedResources = array();
$configCacheFile = dirname(__DIR__) . '/var/resource_config.json';
$relativeFilename = null;

$isAllowed = function ($resource, array $allowedResources) {
    $isResourceAllowed = false;
    foreach ($allowedResources as $allowedResource) {
        if (0 === stripos($resource, $allowedResource)) {
            $isResourceAllowed = true;
        }
    }
    return $isResourceAllowed;
};

if (file_exists($configCacheFile) && is_readable($configCacheFile)) {
    $config = json_decode(file_get_contents($configCacheFile), true);

    //checking update time
    if (filemtime($configCacheFile) + $config['update_time'] > time()) {
        $mediaDirectory = trim(str_replace(__DIR__, '', $config['media_directory']), DS);
        $allowedResources = array_merge($allowedResources, $config['allowed_resources']);
    }
}

// Serve file if it's materialized
$request = new Magento_Core_Model_File_Storage_Request(__DIR__);
if ($mediaDirectory) {
    if (0 !== stripos($request->getPathInfo(), $mediaDirectory . '/') || is_dir($request->getFilePath())) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    $relativeFilename = str_replace($mediaDirectory . '/', '', $request->getPathInfo());
    if (!$isAllowed($relativeFilename, $allowedResources)) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    if (is_readable($request->getFilePath())) {
        $transfer = new \Magento\File\Transfer\Adapter\Http();
        $transfer->send($request->getFilePath());
        exit;
    }
}
// Materialize file in application
$params = $_SERVER;
if (empty($mediaDirectory)) {
    $params[Mage::PARAM_ALLOWED_MODULES] = array('Magento_Core');
    $params[Mage::PARAM_CACHE_OPTIONS]['frontend_options']['disable_save'] = true;
}

$config = new Magento_Core_Model_Config_Primary(dirname(__DIR__), $params);
$entryPoint = new Magento_Core_Model_EntryPoint_Media(
    $config, $request, $isAllowed, __DIR__, $mediaDirectory, $configCacheFile, $relativeFilename
);
$entryPoint->processRequest();
