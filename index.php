<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * require __DIR__ . '/app/bootstrap.php';
 * $params = $_SERVER;
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'website2';
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
 * \/** @var \Magento\Framework\App\Http $app *\/
 * $app = $bootstrap->createApplication('Magento\Framework\App\Http');
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

try {
    require __DIR__ . '/app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);

/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $pluginList  = \Magento\Framework\App\ObjectManager::getInstance()
        ->get(\Magento\Framework\Interception\PluginList\PluginList::class)
        ->get();
    echo '<pre>';
    //var_dump($pluginList);

    /** @var \Magento\Framework\App\Resource $adapter */
    $adapter =  \Magento\Framework\App\ObjectManager::getInstance()
        ->get('Magento\Framework\App\Resource');
    // composer.phar  require "jdorn/sql-formatter:1.3.*@dev"
    // require_once '/home/user/.composer/vendor/jdorn/sql-formatter/lib/SqlFormatter.php';
    /** @var Zend_Db_Profiler $profiler */
    $profiler = $adapter->getConnection('read')->getProfiler();
    echo $requestTime = microtime(1) - $_SERVER['REQUEST_TIME_FLOAT'];
    if ($profiler->getEnabled()) {
        echo "<table cellpadding='0' cellspacing='0' border='0'>";
        echo '<caption>', sprintf('[ %2.4fms ][ %2.4fms ]  - ', $requestTime, $profiler->getTotalElapsedSecs()), $profiler->getTotalNumQueries() , 'queries', '</caption>';
        foreach ($profiler->getQueryProfiles() as $query) {
            /** @var Zend_Db_Profiler_Query $query*/
            echo '<tr>';
            echo '<td>', number_format(1000 * $query->getElapsedSecs(), 2), 'ms', '</td>';
            echo '<td>', $query->getQuery(), '</td>';
            echo '</tr>';
        }
    }
}
