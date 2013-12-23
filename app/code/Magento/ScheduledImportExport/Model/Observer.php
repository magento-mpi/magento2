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
namespace Magento\ScheduledImportExport\Model;

use Magento\Filesystem\FilesystemException;

class Observer
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
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Email\Model\Template\Mailer
     */
    protected $_templateMailer;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory
     */
    protected $_operationFactory;

    /**
     * @var \Magento\Email\Model\InfoFactory
     */
    protected $_emailInfoFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_logDirectory;

    /**
     * @param \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory $operationFactory
     * @param \Magento\Email\Model\InfoFactory $emailInfoFactory
     * @param \Magento\Email\Model\Template\Mailer $templateMailer
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory $operationFactory,
        \Magento\Email\Model\InfoFactory $emailInfoFactory,
        \Magento\Email\Model\Template\Mailer $templateMailer,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Filesystem $filesystem
    ) {
        $this->_operationFactory = $operationFactory;
        $this->_emailInfoFactory = $emailInfoFactory;
        $this->_templateMailer = $templateMailer;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        $this->_logDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::LOG);
    }

    /**
     * Clear old log files and folders
     *
     * @param \Magento\Cron\Model\Schedule $schedule
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
            $logPath = \Magento\ScheduledImportExport\Model\Scheduled\Operation::LOG_DIRECTORY;

            try {
                $this->_logDirectory->create($logPath);
            } catch(FilesystemException $e) {
                throw new \Magento\Core\Exception(__("We couldn't create directory " . '"%1"', $logPath));
            }

            if (!$this->_logDirectory->isWritable($logPath)) {
                throw new \Magento\Core\Exception(__('The directory "%1" is not writable.', $logPath));
            }
            $saveTime = (int) $this->_coreStoreConfig->getConfig(self::SAVE_LOG_TIME_PATH) + 1;
            $dateCompass = new \DateTime('-' . $saveTime . ' days');

            foreach ($this->_getDirectoryList($logPath) as $directory) {
                if (!preg_match('~(\d{4})/(\d{2})/(\d{2})$~', $directory, $matches)) {
                    continue;
                }
                $directoryDate = new \DateTime($matches[1] . '-' . $matches[2] . '-' . $matches[3]);
                if ($forceRun || $directoryDate < $dateCompass) {
                    try {
                        $this->_logDirectory->delete($directory);
                    } catch (FilesystemException $e) {
                        throw new \Magento\Core\Exception(
                            __('We couldn\'t delete "%1" because the directory is not writable.', $directory)
                        );
                    }
                }
            }
            $result = true;
        } catch (\Exception $e) {
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

        $logPath = rtrim($logPath, '/');

        $entities = $this->_logDirectory->read($logPath);
        foreach ($entities as $entity) {
            if (! $this->_logDirectory->isDirectory($entity)) {
                continue;
            }

            $childPath = $logPath . '/' . $entity;
            $mergePart = ($level < 3) ? $this->_getDirectoryList($childPath, $level + 1) : array($childPath);

            $result = array_merge($result, $mergePart);
        }
        return $result;
    }

    /**
     * Run operation in crontab
     *
     * @param \Magento\Cron\Model\Schedule|\Magento\Object $schedule
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
     * @return \Magento\ScheduledImportExport\Model\Observer
     */
    protected function _sendEmailNotification($vars)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $receiverEmail = $this->_coreStoreConfig->getConfig(self::XML_RECEIVER_EMAIL_PATH, $storeId);
        if (!$receiverEmail) {
            return $this;
        }

        /** @var \Magento\Email\Model\Info $emailInfo */
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
