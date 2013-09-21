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
        $coreDir = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Dir');
        $operationFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_ScheduledImportExport_Model_Scheduled_OperationFactory');
        $emailInfoFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Email_InfoFactory');
        $templateMailer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Email_Template_Mailer');
        $storeManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_StoreManager');
        $storeConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Store_Config');
        $model = new Magento_ScheduledImportExport_Model_Observer(
            $coreDir, $operationFactory, $emailInfoFactory, $templateMailer, $storeConfig, $storeManager
        );
        $model->scheduledLogClean('not_used', true);
        /** @var $dirs Magento_Core_Model_Dir */
        $dirs = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Dir');
        $this->assertFileExists($dirs->getDir(Magento_Core_Model_Dir::LOG)
            . DIRECTORY_SEPARATOR
            . Magento_ScheduledImportExport_Model_Scheduled_Operation::LOG_DIRECTORY
        );
    }
}
