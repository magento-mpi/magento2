<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model;

use Magento\Filesystem\FilesystemException;

/**
 * ImportExport module observer
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Observer
{
    /**
     * Cron tab expression path
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr';

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
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory
     */
    protected $_operationFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_logDirectory;

    /**
     * @param \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory $operationFactory
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory $operationFactory,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\Filesystem $filesystem
    ) {
        $this->_operationFactory = $operationFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        $this->_logDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::LOG_DIR);
    }

    /**
     * Clear old log files and folders
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @param bool $forceRun
     * @return bool|void
     */
    public function scheduledLogClean($schedule, $forceRun = false)
    {
        $result = false;
        if (!$this->_storeConfig->getValue(self::CRON_STRING_PATH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE)
            && (!$forceRun || !$this->_storeConfig->getValue(self::LOG_CLEANING_ENABLE_PATH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE))
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
            $saveTime = (int) $this->_storeConfig->getValue(self::SAVE_LOG_TIME_PATH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE) + 1;
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
     * @return string[]
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
     * @return $this
     */
    protected function _sendEmailNotification($vars)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $receiverEmail = $this->_storeConfig->getValue(self::XML_RECEIVER_EMAIL_PATH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $storeId);
        if (!$receiverEmail) {
            return $this;
        }

        // Set all required params and send emails
        /** @var \Magento\Mail\TransportInterface $transport */
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_storeConfig->getValue(self::XML_TEMPLATE_EMAIL_PATH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $storeId))
            ->setTemplateOptions(array(
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ))
            ->setTemplateVars($vars)
            ->setFrom($this->_storeConfig->getValue(self::XML_SENDER_EMAIL_PATH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $storeId))
            ->addTo($receiverEmail)
            ->getTransport();
        $transport->sendMessage();

        return $this;
    }
}
