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
     * @magentoDataFixture Magento/ScheduledImportExport/_files/operation.php
     * @magentoDataFixture Magento/Catalog/_files/products_new.php
     *
     */
    public function testScheduledLogClean()
    {
        // Set up
        /** @var Scheduled\Operation $operation */
        $operation = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation'
        );

        $operation->load('export', 'operation_type');

        $fileInfo = $operation->getFileInfo();
        $historyPath = $operation->getHistoryFilePath();

        // Create export directory if not exist
        /** @var \Magento\Framework\Filesystem\Directory\Write $varDir */
        $varDir = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Filesystem'
        )->getDirectoryWrite(
                'var'
            );
        $varDir->create($fileInfo['file_path']);

        // Change current working directory to allow save export results
        $cwd = getcwd();
        chdir($varDir->getAbsolutePath());

        $operation->run();

        $this->assertFileExists($historyPath);

        // Restore current working directory
        chdir($cwd);

        $operationFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\ScheduledImportExport\Model\Scheduled\OperationFactory'
        );
        $transportBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Mail\Template\TransportBuilder'
        );
        $storeManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManager'
        );
        $scopeConfig = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        );
        /** @var \Magento\Framework\Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Framework\Filesystem');

        $observer = new \Magento\ScheduledImportExport\Model\Observer(
            $operationFactory,
            $transportBuilder,
            $scopeConfig,
            $storeManager,
            $filesystem
        );
        $observer->scheduledLogClean('not_used', true);

        // Verify
        $this->assertFileNotExists($historyPath);
    }
}
