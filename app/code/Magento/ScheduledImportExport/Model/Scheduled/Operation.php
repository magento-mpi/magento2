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
 * Operation model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getOperationType() getOperationType()
 * @method \Magento\ScheduledImportExport\Model\Scheduled\Operation setOperationType() setOperationType(string $value)
 * @method string getEntityType() getEntityType()
 * @method string getEntitySubtype() getEntitySubtype()
 * @method \Magento\ScheduledImportExport\Model\Scheduled\Operation setEntityType() setEntityType(string $value)
 * @method \Magento\ScheduledImportExport\Model\Scheduled\Operation setEntitySubtype() setEntitySubtype(string $value)
 * @method string|array getStartTime() getStartTime()
 * @method \Magento\ScheduledImportExport\Model\Scheduled\Operation setStartTime() setStartTime(string $value)
 * @method string|array getFileInfo() getFileInfo()
 * @method string|array getEntityAttributes() getEntityAttributes()
 * @method string getBehavior() getBehavior()
 * @method string getForceImport() getForceImport()
 * @method \Magento\ScheduledImportExport\Model\Scheduled\Operation setLastRunDate() setLastRunDate(int $value)
 * @method int getLastRunDate() getLastRunDate()
 */
namespace Magento\ScheduledImportExport\Model\Scheduled;

class Operation extends \Magento\Core\Model\AbstractModel
{
    /**
     * Log directory
     *
     */
    const LOG_DIRECTORY = 'import_export/';

    /**
     * File history directory
     *
     */
    const FILE_HISTORY_DIRECTORY = 'history';

    /**
     * Email config prefix
     */
    const CONFIG_PREFIX_EMAILS = 'trans_email/ident_';

    /**
     * Cron config template
     */
    const CRON_STRING_PATH = 'crontab/jobs/scheduled_operation_%d/%s';

    /**
     * Cron callback config
     */
    const CRON_MODEL = 'Magento\ScheduledImportExport\Model\Observer::processScheduledOperation';

    /**
     * Cron job name prefix
     */
    const CRON_JOB_NAME_PREFIX = 'scheduled_operation_';

    /**
     * Date model
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_dateModel;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Email\Template\Mailer
     */
    protected $_templateMailer;

    /**
     * @var \Magento\Core\Model\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var \Magento\Core\Model\Email\InfoFactory
     */
    protected $_emailInfoFactory;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory
     */
    protected $_operationFactory;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory
     */
    protected $_schedOperFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_coreDir;

    /**
     * @param \Magento\Core\Model\Dir $coreDir
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory $schedOperFactory
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory $operationFactory
     * @param \Magento\Core\Model\Email\InfoFactory $emailInfoFactory
     * @param \Magento\Core\Model\Config\ValueFactory $configValueFactory
     * @param \Magento\Core\Model\Email\Template\Mailer $templateMailer
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Date $dateModel
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Dir $coreDir,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory $schedOperFactory,
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory $operationFactory,
        \Magento\Core\Model\Email\InfoFactory $emailInfoFactory,
        \Magento\Core\Model\Config\ValueFactory $configValueFactory,
        \Magento\Core\Model\Email\Template\Mailer $templateMailer,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Date $dateModel,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_dateModel = $dateModel;
        $this->_templateMailer = $templateMailer;
        $this->_configValueFactory = $configValueFactory;
        $this->_emailInfoFactory = $emailInfoFactory;
        $this->_operationFactory = $operationFactory;
        $this->_schedOperFactory = $schedOperFactory;
        $this->_storeManager = $storeManager;
        $this->_coreDir = $coreDir;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_init('Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation');
    }

    /**
     * Date model getter
     *
     * @return \Magento\Core\Model\Date
     */
    public function getDateModel()
    {
        return $this->_dateModel;
    }

    /**
     * Send email notification
     *
     * @param array $vars
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    public function sendEmailNotification($vars = array())
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $copyTo = explode(',', $this->getEmailCopy());
        $copyMethod = $this->getEmailCopyMethod();

        /** @var \Magento\Core\Model\Email\Info $emailInfo */
        $emailInfo = $this->_emailInfoFactory->create();

        $receiverEmail = $this->_coreStoreConfig->getConfig(
            self::CONFIG_PREFIX_EMAILS . $this->getEmailReceiver() . '/email',
            $storeId
        );
        $receiverName  = $this->_coreStoreConfig->getConfig(
            self::CONFIG_PREFIX_EMAILS . $this->getEmailReceiver() . '/name',
            $storeId
        );

        $emailInfo->addTo($receiverEmail, $receiverName);

        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        $this->_templateMailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                /** @var \Magento\Core\Model\Email\Info $emailInfo */
                $emailInfo = $this->_emailInfoFactory->create();
                $emailInfo->addTo($email);
                $this->_templateMailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $this->_templateMailer->setSender($this->getEmailSender());
        $this->_templateMailer->setStoreId($storeId);
        $this->_templateMailer->setTemplateId($this->getEmailTemplate());
        $this->_templateMailer->setTemplateParams($vars);
        $this->_templateMailer->send();
        return $this;
    }

    /**
     * Unserialize file_info and entity_attributes after load
     *
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected function _afterLoad()
    {
        $fileInfo = $this->getFileInfo();
        if (trim($fileInfo)) {
            $this->setFileInfo(unserialize($fileInfo));
        }

        $attrsInfo = $this->getEntityAttributes();
        if (trim($attrsInfo)) {
            $this->setEntityAttributes(unserialize($attrsInfo));
        }

        return parent::_afterLoad();
    }

    /**
     * Serialize file_info and entity_attributes arrays before save
     *
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected function _beforeSave()
    {
        $fileInfo = $this->getFileInfo();
        if (is_array($fileInfo) && $fileInfo) {
            $this->setFileInfo(serialize($fileInfo));
        }

        $attrsInfo = $this->getEntityAttributes();
        if (is_array($attrsInfo) && $attrsInfo) {
            $this->setEntityAttributes(serialize($attrsInfo));
        }

        return parent::_beforeSave();
    }

    /**
     * Add task to cron after save
     *
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected function _afterSave()
    {
        if ($this->getStatus() == 1) {
            $this->_addCronTask();
        } else {
            $this->_dropCronTask();
        }
        return parent::_afterSave();
    }

    /**
     * Delete cron task
     *
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected function _afterDelete()
    {
        $this->_dropCronTask();
        return parent::_afterDelete();
    }

    /**
     * Add operation to cron
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected function _addCronTask()
    {
        $frequency = $this->getFreq();
        $time = $this->getStartTime();
        if (!is_array($time)) {
            $time = explode(':', $time);
        }
        $cronExprArray = array(
            intval($time[1]),
            intval($time[0]),
            ($frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY) ? '1' : '*',
            '*',
            ($frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY) ? '1' : '*'
        );

        $cronExprString = join(' ', $cronExprArray);
        $exprPath  = $this->getExprConfigPath();
        $modelPath = $this->getModelConfigPath();
        try {
            $this->_configValueFactory->create()
                ->load($exprPath, 'path')
                ->setValue($cronExprString)
                ->setPath($exprPath)
                ->save();

            $this->_configValueFactory->create()
                ->load($modelPath, 'path')
                ->setValue(self::CRON_MODEL)
                ->setPath($modelPath)
                ->save();
        } catch (\Exception $e) {
            throw new \Magento\Core\Exception(__('We were unable to save the cron expression.'));
            $this->_logger->logException($e);
        }
        return $this;
    }

    /**
     * Remove cron task
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected function _dropCronTask()
    {
        try {
            $this->_configValueFactory->create()
                ->load($this->getExprConfigPath(), 'path')
                ->delete();
            $this->_configValueFactory->create()
                ->load($this->getModelConfigPath(), 'path')
                ->delete();
        } catch (\Exception $e) {
            throw new \Magento\Core\Exception(__('Unable to delete the cron task.'));
            $this->_logger->logException($e);
        }
        return $this;
    }

    /**
     * Get cron_expr config path
     *
     * @return string
     */
    public function getExprConfigPath()
    {
        return sprintf(self::CRON_STRING_PATH, $this->getId(), 'schedule/cron_expr');
    }

    /**
     * Get cron callback model config path
     *
     * @return string
     */
    public function getModelConfigPath()
    {
        return sprintf(self::CRON_STRING_PATH, $this->getId(), 'run/model');
    }

    /**
     * Load operation by cron job code.
     * Operation id must present in job code.
     *
     * @throws \Magento\Core\Exception
     * @param string $jobCode
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    public function loadByJobCode($jobCode)
    {
        $idPos = strrpos($jobCode, '_');
        if ($idPos !== false) {
            $operationId = (int)substr($jobCode, $idPos + 1);
        }
        if (!isset($operationId) || !$operationId) {
            throw new \Magento\Core\Exception(__('Please correct the cron job task'));
        }

        return $this->load($operationId);
    }

    /**
     * Run scheduled operation. If some error occurred email notification will be send
     *
     * @return bool
     */
    public function run()
    {
        $runDate = $this->getDateModel()->date();
        $runDateTimestamp = $this->getDateModel()->gmtTimestamp($runDate);

        $this->setLastRunDate($runDateTimestamp);

        $operation = $this->getInstance();
        $operation->setRunDate($runDateTimestamp);

        $result = false;
        try {
            $result = $operation->runSchedule($this);
        } catch (\Exception $e) {
            $operation->addLogComment($e->getMessage());
        }

        $filePath = $this->getHistoryFilePath();
        if (!file_exists($filePath)) {
            $filePath = __('File has been not created');
        }

        if (!$result || isset($e) && is_object($e)) {
            $operation->addLogComment(
                __('Something went wrong and the operation failed.')
            );
            $this->sendEmailNotification(array(
                'operationName'  => $this->getName(),
                'trace'          => nl2br($operation->getFormatedLogTrace()),
                'entity'         => $this->getEntityType(),
                'dateAndTime'    => $runDate,
                'fileName'       => $filePath
            ));
        }

        $this->setIsSuccess($result);
        $this->save();

        return $result;
    }

    /**
     * Get file based on "file_info" from server (ftp, local) and put to tmp directory
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation
     * @throws \Magento\Core\Exception
     * @return string full file path
     */
    public function getFileSource(\Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation)
    {
        $fileInfo = $this->getFileInfo();
        if (empty($fileInfo['file_name'])) {
            throw new \Magento\Core\Exception(__("We couldn't read the file source because the file name is empty."));
        }
        $operation->addLogComment(__('Connecting to server'));
        $fs = $this->getServerIoDriver();
        $operation->addLogComment(__('Reading import file'));

        $extension = pathinfo($fileInfo['file_name'], PATHINFO_EXTENSION);
        $filePath  = $fileInfo['file_name'];
        $tmpFilePath = sys_get_temp_dir() . DS . uniqid(time(), true) . '.' . $extension;
        if (!$fs->read($filePath, $tmpFilePath)) {
            throw new \Magento\Core\Exception(__("We couldn't read the import file."));
        }
        $fs->close();
        $operation->addLogComment(__('Save history file content "%1"', $this->getHistoryFilePath()));
        $this->_saveOperationHistory($tmpFilePath);
        return $tmpFilePath;
    }

    /**
     * Save/upload file to server (ftp, local)
     *
     * @throws \Magento\Core\Exception
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation
     * @param string $fileContent
     * @return bool
     */
    public function saveFileSource(
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation,
        $fileContent
    ) {
        $result = false;

        $operation->addLogComment(__('Save history file content "%1"', $this->getHistoryFilePath()));
        $this->_saveOperationHistory($fileContent);

        $fileInfo = $this->getFileInfo();
        $fs       = $this->getServerIoDriver();
        $fileName = $operation->getScheduledFileName() . '.' . $fileInfo['file_format'];
        $result   = $fs->write($fileName, $fileContent);
        if (!$result) {
            throw new \Magento\Core\Exception(
                __('We couldn\'t write file "%1" to "%2" with the "%3" driver.', $fileName, $fileInfo['file_path'], $fileInfo['server_type'])
            );
        }
        $operation->addLogComment(__('Save file content'));

        $fs->close();

        return $result;
    }

    /**
     * Get operation instance by operation type and set specific data to it
     * Supported import, export
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
     */
    public function getInstance()
    {
        /** @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation */
        $operation = $this->_schedOperFactory->create(
            'Magento\ScheduledImportExport\Model\\' . uc_words($this->getOperationType())
        );

        $operation->initialize($this);
        return $operation;
    }

    /**
     * Get and initialize file system driver by operation file section configuration
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Io\Abstract
     */
    public function getServerIoDriver()
    {
        $fileInfo = $this->getFileInfo();
        $availableTypes = $this->_operationFactory->create()
            ->getServerTypesOptionArray();
        if (!isset($fileInfo['server_type'])
            || !$fileInfo['server_type']
            || !isset($availableTypes[$fileInfo['server_type']])
        ) {
            throw new \Magento\Core\Exception(__('Please correct the server type.'));
        }

        $class = 'Magento\\Io\\' . ucfirst(strtolower($fileInfo['server_type']));
        if (!class_exists($class)) {
            throw new \Magento\Core\Exception(__('Please correct the server communication class "%1".', $class));
        }
        $driver = new $class;
        $driver->open($this->_prepareIoConfiguration($fileInfo));
        return $driver;
    }

    /**
     * Prepare data for server io driver initialization
     *
     * @param array $fileInfo
     * @return array prepared configuration
     */
    protected function _prepareIoConfiguration($fileInfo)
    {
        $data = array();
        foreach ($fileInfo as $key => &$v) {
            $key = str_replace('file_', '', $key);
            $data[$key] = $v;
        }
        unset($data['format'], $data['server_type'], $data['name']);
        if (isset($data['mode'])) {
            $data['file_mode'] = $data['mode'];
            unset($data['mode']);
        }
        if (isset($data['host']) && strpos($data['host'], ':') !== false) {
            $tmp = explode(':', $data['host']);
            $data['port'] = array_pop($tmp);
            $data['host'] = join(':', $tmp);
        }

        return $data;
    }

    /**
     * Save operation file history.
     *
     * @throws \Magento\Core\Exception
     * @param string $source
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected function _saveOperationHistory($source)
    {
        $filePath = $this->getHistoryFilePath();

        $fs = new \Magento\Io\File();
        $fs->open(array(
            'path' => dirname($filePath)
        ));
        if (!$fs->write(basename($filePath), $source)) {
            throw new \Magento\Core\Exception(__("We couldn't save the file history file."));
        }
        return $this;
    }

    /**
     * Get dir path of history operation files
     *
     * @return string
     */
    protected function _getHistoryDirPath()
    {
        $dirPath = $this->_coreDir->getDir(\Magento\Core\Model\Dir::LOG) . DS . self::LOG_DIRECTORY
            . date('Y' . DS . 'm' . DS . 'd') . DS . self::FILE_HISTORY_DIRECTORY . DS;

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        return $dirPath;
    }

    /**
     * Get file path of history operation files
     *
     * @throws \Magento\Core\Exception
     * @return string
     */
    public function getHistoryFilePath()
    {
        $dirPath = $this->_getHistoryDirPath();

        $fileName = join('_', array(
            $this->_getRunTime(),
            $this->getOperationType(),
            $this->getEntityType()
        ));

        $fileInfo = $this->getFileInfo();
        if (isset($fileInfo['file_format'])) {
            $extension = $fileInfo['file_format'];
        } elseif (isset($fileInfo['file_name'])) {
            $extension = pathinfo($fileInfo['file_name'], PATHINFO_EXTENSION);
        } else {
            throw new \Magento\Core\Exception(__('Unknown file format'));
        }

        return $dirPath . $fileName . '.' . $extension;
    }

    /**
     * Get current time
     *
     * @return string
     */
    protected function _getRunTime()
    {
        $runDate = $this->getLastRunDate() ? $this->getLastRunDate() : null;
        return $this->getDateModel()->date('H-i-s', $runDate);
    }
}
