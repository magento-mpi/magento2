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
     * @codingStandardsIgnoreStart
     * @magentoConfigFixture current_store crontab/default/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr 1
     * @codingStandardsIgnoreEnd
     */
    public function testScheduledLogClean()
    {
        $operationFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\ScheduledImportExport\Model\Scheduled\OperationFactory'
        );
        $transportBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Mail\Template\TransportBuilder'
        );
        $storeManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManager'
        );
        $scopeConfig = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        );
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Framework\App\Filesystem');
        $model = new \Magento\ScheduledImportExport\Model\Observer(
            $operationFactory,
            $transportBuilder,
            $scopeConfig,
            $storeManager,
            $filesystem
        );
        $model->scheduledLogClean('not_used', true);

        $this->assertFileExists(
            $filesystem->getPath(
                \Magento\Framework\App\Filesystem::LOG_DIR
            ) . '/' . \Magento\ScheduledImportExport\Model\Scheduled\Operation::LOG_DIRECTORY
        );
    }
}
