<?php
/**
 * Public media files entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__DIR__) . '/app/bootstrap.php';

$mediaDirectory = null;
$allowedResources = array();
$configCacheFile = dirname(__DIR__) . '/var/resource_config.json';
if (file_exists($configCacheFile) && is_readable($configCacheFile)) {
    $config = json_decode(file_get_contents($configCacheFile), true);

    //checking update time
    if (filemtime($configCacheFile) + $config['update_time'] > time()) {
        $mediaDirectory = trim(str_replace(__DIR__, '', $config['media_directory']), DS);
        $allowedResources = array_merge($allowedResources, $config['allowed_resources']);
    }
}

$request = new Zend_Controller_Request_Http();

$pathInfo = str_replace('..', '', ltrim($request->getPathInfo(), '/'));

$filePath = str_replace('/', DS, __DIR__ . DS . $pathInfo);

if ($mediaDirectory) {
    if (0 !== stripos($pathInfo, $mediaDirectory . '/') || is_dir($filePath)) {
        sendNotFoundPage();
    }

    $relativeFilename = str_replace($mediaDirectory . '/', '', $pathInfo);
    checkResource($relativeFilename, $allowedResources);
    sendFile($filePath);
}
try {
    /** @var $app Mage_Core_Model_App */
    $app = self::getObjectManager()->create('Mage_Core_Model_App');
    Mage::setApp($app);

    if (empty($mediaDirectory)) {
        $app->init($_SERVER);
    } else {
        $params = array_merge(
            $_SERVER,
            array(Mage_Core_Model_Cache::APP_INIT_PARAM => array('disallow_save' => true))
        );
        $app->init($params, array('Mage_Core'));
    }

} catch (Mage_Core_Model_Session_Exception $e) {
    header('Location: ' . self::getBaseUrl());
    die;
} catch (Mage_Core_Model_Store_Exception $e) {
    require_once(self::getBaseDir(Mage_Core_Model_Dir::PUB) . DS . 'errors' . DS . '404.php');
    die;
} catch (Exception $e) {
    self::printException($e);
    die;
}

Mage::app()->requireInstalledInstance();

if (!$mediaDirectory) {
    $config = Mage_Core_Model_File_Storage::getScriptConfig();
    $mediaDirectory = str_replace(__DIR__, '', $config['media_directory']);
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
    $databaseFileStorage = Mage::getModel('Mage_Core_Model_File_Storage_Database');
    $databaseFileStorage->loadByFilename($relativeFilename);
} catch (Exception $e) {
}
if ($databaseFileStorage->getId()) {
    $directory = dirname($filePath);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $fp = fopen($filePath, 'w');
    if (flock($fp, LOCK_EX | LOCK_NB)) {
        ftruncate($fp, 0);
        fwrite($fp, $databaseFileStorage->getContent());
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
