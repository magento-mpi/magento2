<?php

/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */
require_once 'app/bootstrap.php';

$varDirectory = BP . DS . Mage_Core_Model_Config_Options::VAR_DIRECTORY;

$configCacheFile = $varDirectory . DS . 'resource_config.json';

$mediaDirectory = null;
$allowedResources = array();

if (file_exists($configCacheFile) && is_readable($configCacheFile)) {
    $config = json_decode(file_get_contents($configCacheFile), true);

    //checking update time
    if (filemtime($configCacheFile) + $config['update_time'] > time()) {
        $mediaDirectory = trim(str_replace(BP . DS, '', $config['media_directory']), DS);
        $allowedResources = array_merge($allowedResources, $config['allowed_resources']);
    }
}

$request = new Zend_Controller_Request_Http();

$pathInfo = str_replace('..', '', ltrim($request->getPathInfo(), '/'));

$filePath = str_replace('/', DS, rtrim(BP, DS) . DS . $pathInfo);

if ($mediaDirectory) {
    if (0 !== stripos($pathInfo, $mediaDirectory . '/') || is_dir($filePath)) {
        sendNotFoundPage();
    }

    $relativeFilename = str_replace($mediaDirectory . '/', '', $pathInfo);
    checkResource($relativeFilename, $allowedResources);
    sendFile($filePath);
}

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

if (empty($mediaDirectory)) {
    Mage::init($mageRunCode, $mageRunType);
} else {
    Mage::init(
        $mageRunCode,
        $mageRunType,
        array('cache' => array('disallow_save' => true)),
        array('Mage_Core')
    );
}

if (!$mediaDirectory) {
    $config = Mage_Core_Model_File_Storage::getScriptConfig();
    $mediaDirectory = str_replace(BP . DS, '', $config['media_directory']);
    $allowedResources = array_merge($allowedResources, $config['allowed_resources']);

    $relativeFilename = str_replace($mediaDirectory . '/', '', $pathInfo);

    $fp = fopen($configCacheFile, 'w');
    if (flock($fp, LOCK_EX | LOCK_NB)) {
        ftruncate($fp, 0);
        fwrite($fp, json_encode($config));
    }
    flock($fp, LOCK_UN);
    fclose($fp);

    checkResource($relativeFilename, $allowedResources);
}

if (0 !== stripos($pathInfo, $mediaDirectory . '/')) {
    sendNotFoundPage();
}

try {
    $databaseFileSotrage = Mage::getModel('Mage_Core_Model_File_Storage_Database');
    $databaseFileSotrage->loadByFilename($relativeFilename);
} catch (Exception $e) {
}
if ($databaseFileSotrage->getId()) {
    $directory = dirname($filePath);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $fp = fopen($filePath, 'w');
    if (flock($fp, LOCK_EX | LOCK_NB)) {
        ftruncate($fp, 0);
        fwrite($fp, $databaseFileSotrage->getContent());
    }
    flock($fp, LOCK_UN);
    fclose($fp);
}

sendFile($filePath);
sendNotFoundPage();

/**
 * Send 404
 */
function sendNotFoundPage()
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * Check resource by whitelist
 *
 * @param string $resource
 * @param array $allowedResources
 */
function checkResource($resource, array $allowedResources)
{
    $isResourceAllowed = false;
    foreach ($allowedResources as $allowedResource) {
        if (0 === stripos($resource, $allowedResource)) {
            $isResourceAllowed = true;
        }
    }

    if (!$isResourceAllowed) {
        sendNotFoundPage();
    }
}
/**
 * Send file to browser
 *
 * @param string $file
 */
function sendFile($file)
{
    if (file_exists($file) || is_readable($file)) {
        $transfer = new Varien_File_Transfer_Adapter_Http();
        $transfer->send($file);
        exit;
    }
}
