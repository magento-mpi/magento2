<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store crontab/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr 1
     */
    public function testScheduledLogClean()
    {
        $model = new \Magento\ScheduledImportExport\Model\Observer;
        $model->scheduledLogClean('not_used', true);
        /** @var $dirs \Magento\Core\Model\Dir */
        $dirs = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir');
        $this->assertFileExists($dirs->getDir(\Magento\Core\Model\Dir::LOG)
            . DIRECTORY_SEPARATOR
            . \Magento\ScheduledImportExport\Model\Scheduled\Operation::LOG_DIRECTORY
        );
    }
}
