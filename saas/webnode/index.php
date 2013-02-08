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
     * url comes with uppercase letters.
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
    $saasDb = new Saas_Db(SAAS_MONGO_DSN, SAAS_MONGO_DATABASE, $options);
    $codeBase = $saasDb->getTenantCodeBase($_SERVER['HTTP_HOST'], SAAS_MAGENTO_DIR);
    if ($codeBase->isUnderMaintenance()) {
        throw new Exception(sprintf('Tenant "%s" is currently under maintenance.', $codeBase->getId()));
    }
    $magentoDir = $codeBase->getDir();

    if (version_compare($codeBase->getVersion(), '2.0.0.0-dev01') === -1) {
        // backwards-compatibility hacks for SaaS 1.x. See comment for each line below
        set_include_path($magentoDir); // usage is unknown
        Saas_Db::setInstance($saasDb); // actively used in application code
        $tenant = $saasDb->getTenantById($codeBase->getId()); // used application entry point as a global variable (!)

        //Process robots.txt request
        if ($_SERVER['REQUEST_URI'] == '/robots.txt') {
            $robotsFile = $magentoDir . '/' . $tenant->getMediaDir() . '/robots.txt';
            if (!file_exists($robotsFile)) {
                $robotsFile = $magentoDir . '/robots.txt';
            }

            readfile($robotsFile);
        } else {
            // turn magento API logging on
            include_once __DIR__ . '/apiLog.php';

            $indexFile = $magentoDir
                . (file_exists($magentoDir . '/index_saas.php') ? '/index_saas.php' : '/index.php');
            require $indexFile;
        }
    } else {
        /** @var $appEntryPoint callback */
        $appEntryPoint = require $magentoDir . '/saas.php';

        $appEntryPoint($saasDb->getTenantData($codeBase->getId()));
    }

} catch (Saas_Db_WrongTenantException $e) {
    header('Location: ' . SAAS_WEBNODE_NO_TENANT_LINK, true, 302);
} catch (Exception $e) {
    header("{$_SERVER['SERVER_PROTOCOL']} 503 Service Temporarily Unavailable", true, 503);
    include(__DIR__ . '/static/maintenance.html');
    trigger_error("{$e->getMessage()}\n{$e->getTraceAsString()}", E_USER_ERROR);
}
