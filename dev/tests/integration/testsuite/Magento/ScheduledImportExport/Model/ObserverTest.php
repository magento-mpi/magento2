<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ScheduledImportExport_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store crontab/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr 1
     */
    public function testScheduledLogClean()
    {
        $storeConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Store\Config');
        $model = new \Magento\ScheduledImportExport\Model\Observer($storeConfig);
        $model->scheduledLogClean('not_used', true);
        /** @var $dirs \Magento\Core\Model\Dir */
        $dirs = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir');
        $this->assertFileExists($dirs->getDir(\Magento\Core\Model\Dir::LOG)
            . DIRECTORY_SEPARATOR
            . \Magento\ScheduledImportExport\Model\Scheduled\Operation::LOG_DIRECTORY
        );
    }
}
