<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * $params = $_SERVER;
 * $params[StoreManager::PARAM_RUN_CODE] = 'website2';
 * $params[StoreManager::PARAM_RUN_TYPE] = 'website';
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
 * \/** @var \Magento\Framework\App\Http $app *\/
 * $app = $bootstrap->createApplication('Magento\Framework\App\Http');
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    /** @var \Magento\Framework\App\Resource $adapter */
    $adapter =  \Magento\Framework\App\ObjectManager::getInstance()
        ->get('Magento\Framework\App\Resource');
    // composer.phar  require "jdorn/sql-formatter:1.3.*@dev"
    require_once '/home/user/.composer/vendor/jdorn/sql-formatter/lib/SqlFormatter.php';
    /** @var Zend_Db_Profiler $profiler */
    $profiler = $adapter->getConnection('read')->getProfiler();
    echo "<table cellpadding='0' cellspacing='0' border='0'>";
    echo '<caption>', $profiler->getTotalElapsedSecs(), 's ', $profiler->getTotalNumQueries() , 'queries', '</caption>';
    foreach ($profiler->getQueryProfiles() as $query) {
        /** @var Zend_Db_Profiler_Query $query*/
        echo '<tr>';
        echo '<td>', number_format(1000 * $query->getElapsedSecs(), 2), 'ms', '</td>';
        echo '<td>', SqlFormatter::format($query->getQuery()), '</td>';
        echo '<td><pre>', preg_replace('/[\\[\\]\\{\\}\\"'."\t".' ,]/', '', json_encode($query->getQueryParams(), JSON_PRETTY_PRINT)), '</pre></td>';
        echo '</tr>';
    }
}
