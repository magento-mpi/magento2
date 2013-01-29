<?php
/**
 * Saas webnode entry point
 */
require dirname(__DIR__) . '/bootstrap.php';
try {
    /**
     * Only lowercase urls are allowed.
     * In common cases lowercase urls are used, but in some rare cases
     * (for example, when url is added to your facebook status)
     * url comes with upeercase letters.
     * If url comes with uppercase letters, tenant is not found and responce with 302 code is returned
     */
    $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
    $options = array();
    if (SAAS_MONGO_IS_PERSIST) {
        $options['persist'] = SAAS_MONGO_IS_PERSIST;
    }
    if (SAAS_MONGO_IS_REPLICA) {
        $options['replicaSet'] = SAAS_MONGO_IS_REPLICA;
    }
    Saas_Db::init(SAAS_MONGO_DSN, SAAS_MONGO_DATABASE, $options);
    $codeBase = Saas_Db::getInstance()->getTenantCodeBase($_SERVER['HTTP_HOST'], SAAS_MAGENTO_DIR);
    if ($codeBase->isUnderMaintenance()) {
        throw new Exception(sprintf('Tenant "%s" is currently under maintenance.', $codeBase->getId()));
    }
    $magentoDir = $codeBase->getDir();
} catch (Saas_Db_WrongTenantException $e) {
    header('Location: ' . SAAS_WEBNODE_NO_TENANT_LINK, true, 302);
    exit;
} catch (Exception $e) {
    header("{$_SERVER['SERVER_PROTOCOL']} 503 Service Temporarily Unavailable", true, 503);
    include(dirname(__FILE__) . '/static/maintenance.html');
    trigger_error("{$e->getMessage()}\n{$e->getTraceAsString()}", E_USER_ERROR);
    exit;
}

// Process robots.txt request
if ($_SERVER['REQUEST_URI'] == '/robots.txt') {
    $robotsFile = $magentoDir . DIRECTORY_SEPARATOR . $codeBase->getMediaDirName() . DIRECTORY_SEPARATOR . 'robots.txt';
    if (!file_exists($robotsFile)) {
        $robotsFile = $magentoDir . DIRECTORY_SEPARATOR . 'robots.txt';
    }

    readfile($robotsFile);
    exit();
}

// turn magento API logging on
include_once __DIR__ . '/apiLog.php';

set_include_path($magentoDir);
if (version_compare($codeBase->getVersion(), '2.0.0.0') === -1) {
    $indexFile = $magentoDir . (file_exists($magentoDir . '/index_saas.php') ? '/index_saas.php' : '/index.php');
    require $indexFile;
} else {
    $appEntryPoint = require $magentoDir . DIRECTORY_SEPARATOR . 'saas.php';
    $appEntryPoint(Saas_Db::getInstance()->getTenantData($codeBase->getId()));
}
