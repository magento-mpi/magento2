<?php
/**
 * Scheduled jobs entry point
 *
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__DIR__) . '/app/bootstrap.php';
\Magento\Profiler::start('magento');
umask(0);

try {
    $params = array(
        \Magento\Core\Model\App::PARAM_RUN_CODE => 'admin',
        \Magento\Core\Model\Store::CUSTOM_ENTRY_POINT_PARAM => true
    );
    $entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $params);
    $entryPoint->run(
        'Magento\App\Cron',
        array('parameters' => array('group::'))
    );
} catch (\Exception $e) {
    print $e->getMessage() . "\n\n";
    print $e->getTraceAsString();
}
\Magento\Profiler::stop('magento');
