<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ImportExport module observer
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ScheduledImportExport_Model_Observer
{
    /**
     * Cron tab expression path
     */
    const CRON_STRING_PATH = 'crontab/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr';

    /**
     * Configuration path of log status
     */
    const LOG_CLEANING_ENABLE_PATH = 'system/magento_scheduled_import_export_log/enabled';

    /**
     * Configuration path of log save days
     */
    const SAVE_LOG_TIME_PATH = 'system/magento_scheduled_import_export_log/save_days';

    /**
     * Recipient email configuraiton path
     */
    const XML_RECEIVER_EMAIL_PATH = 'system/magento_scheduled_import_export_log/error_email';

    /**
     * Sender email configuraiton path
     */
    const XML_SENDER_EMAIL_PATH   = 'system/magento_scheduled_import_export_log/error_email_identity';

    /**
     * Email template configuraiton path
     */
    const XML_TEMPLATE_EMAIL_PATH = 'system/magento_scheduled_import_export_log/error_email_template';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_Email_Template_Mailer
     */
    protected $_templateMailer;

    /**
     * @var Magento_ScheduledImportExport_Model_Scheduled_OperationFactory
     */
    protected $_operationFactory;

    /**
     * @var Magento_Core_Model_Email_InfoFactory
     */
    protected $_emailInfoFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_coreDir;

    /**
     * @param Magento_Core_Model_Dir $coreDir
     * @param Magento_ScheduledImportExport_Model_Scheduled_OperationFactory $operationFactory
     * @param Magento_Core_Model_Email_InfoFactory $emailInfoFactory
     * @param Magento_Core_Model_Email_Template_Mailer $templateMailer
     * @param Magento_Core_Model_Store_ConfigInterface $coreStoreConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Core_Model_Dir $coreDir,
        Magento_ScheduledImportExport_Model_Scheduled_OperationFactory $operationFactory,
        Magento_Core_Model_Email_InfoFactory $emailInfoFactory,
        Magento_Core_Model_Email_Template_Mailer $templateMailer,
        Magento_Core_Model_Store_ConfigInterface $coreStoreConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_operationFactory = $operationFactory;
        $this->_emailInfoFactory = $emailInfoFactory;
        $this->_templateMailer = $templateMailer;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        $this->_coreDir = $coreDir;
    }

    /**
     * Clear old log files and folders
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @param bool $forceRun
     * @return bool
     */
    public function scheduledLogClean($schedule, $forceRun = false)
    {
        $result = false;
        if (!$this->_coreStoreConfig->getConfig(self::CRON_STRING_PATH)
            && (!$forceRun || !$this->_coreStoreConfig->getConfig(self::LOG_CLEANING_ENABLE_PATH))
        ) {
            return;
        }

        try {
            $logPath = $this->_coreDir->getDir(Magento_Core_Model_Dir::LOG)
                . DS . Magento_ScheduledImportExport_Model_Scheduled_Operation::LOG_DIRECTORY;

            if (!file_exists($logPath) || !is_dir($logPath)) {
                if (!mkdir($logPath, 0777, true)) {
                    throw new Magento_Core_Exception(__("We couldn't create directory " . '"%1"', $logPath));
                }
            }

            if (!is_dir($logPath) || !is_writable($logPath)) {
                throw new Magento_Core_Exception(__('The directory "%1" is not writable.', $logPath));
            }
            $saveTime = (int) $this->_coreStoreConfig->getConfig(self::SAVE_LOG_TIME_PATH) + 1;
            $dateCompass = new DateTime('-' . $saveTime . ' days');

            foreach ($this->_getDirectoryList($logPath) as $directory) {
                $separator = str_replace('\\', '\\\\', DS);
                if (!preg_match("~(\d{4})$separator(\d{2})$separator(\d{2})$~", $directory, $matches)) {
                    continue;
                }

                $direcotryDate = new DateTime($matches[1] . '-' . $matches[2] . '-' . $matches[3]);
                if ($forceRun || $direcotryDate < $dateCompass) {
                    $fs = new Magento_Io_File();
                    if (!$fs->rmdirRecursive($directory, true)) {
                        $directory = str_replace($this->_coreDir->getDir() . DS, '', $directory);
                        throw new Magento_Core_Exception(
                            __('We couldn\'t delete "%1" because the directory is not writable.', $directory)
                        );
                    }
                }
            }
            $result = true;
        } catch (Exception $e) {
            $this->_sendEmailNotification(array(
                'warnings' => $e->getMessage()
            ));
        }
        return $result;
    }

    /**
     * Parse log folder filesystem and find all directories on third nesting level
     *
     * @param string $logPath
     * @param int $level
     * @return array
     */
    protected function _getDirectoryList($logPath, $level = 1)
    {
        $result = array();

        $logPath = rtrim($logPath, DS);
        $fs = new Magento_Io_File();
        $fs->cd($logPath);

        foreach ($fs->ls() as $entity) {
            if ($entity['leaf']) {
                continue;
            }

            $childPath = $logPath . DS . $entity['text'];
            $mergePart = ($level < 3) ? $this->_getDirectoryList($childPath, $level + 1) : array($childPath);

            $result = array_merge($result, $mergePart);
        }
        return $result;
    }

    /**
     * Run operation in crontab
     *
     * @param Magento_Cron_Model_Schedule|Magento_Object $schedule
     * @param bool $forceRun
     * @return bool
     */
    public function processScheduledOperation($schedule, $forceRun = false)
    {
        $operation = $this->_operationFactory->create()
            ->loadByJobCode($schedule->getJobCode());

        $result = false;
        if ($operation && ($operation->getStatus() || $forceRun)) {
            $result = $operation->run();
        }

        return $result;
    }

    /**
     * Send email notification
     *
     * @param array $vars
     * @return Magento_ScheduledImportExport_Model_Observer
     */
    protected function _sendEmailNotification($vars)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $receiverEmail = $this->_coreStoreConfig->getConfig(self::XML_RECEIVER_EMAIL_PATH, $storeId);
        if (!$receiverEmail) {
            return $this;
        }

        /** @var Magento_Core_Model_Email_Info $emailInfo */
        $emailInfo = $this->_emailInfoFactory->create();
        $emailInfo->addTo($receiverEmail);

        $this->_templateMailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $this->_templateMailer->setSender($this->_coreStoreConfig->getConfig(self::XML_SENDER_EMAIL_PATH, $storeId));
        $this->_templateMailer->setStoreId($storeId);
        $this->_templateMailer->setTemplateId(
            $this->_coreStoreConfig->getConfig(self::XML_TEMPLATE_EMAIL_PATH, $storeId)
        );
        $this->_templateMailer->setTemplateParams($vars);
        $this->_templateMailer->send();
        return $this;
    }
}
