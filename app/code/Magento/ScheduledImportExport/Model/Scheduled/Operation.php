<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Scheduled;

use Magento\ScheduledImportExport\Model\Scheduled\Operation\Data;

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
class Operation extends \Magento\Framework\Model\AbstractModel
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
    const FILE_HISTORY_DIRECTORY = 'history/';

    /**
     * Email config prefix
     */
    const CONFIG_PREFIX_EMAILS = 'trans_email/ident_';

    /**
     * Cron config template
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/scheduled_operation_%d/%s';

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
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_dateModel;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory
     */
    protected $_operationFactory;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory
     */
    protected $_schedOperFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * Filesystem instance
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Io\Ftp
     */
    protected $ftpAdapter;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory $schedOperFactory
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory $operationFactory
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Stdlib\DateTime\DateTime $dateModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Io\Ftp $ftpAdapter
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory $schedOperFactory,
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory $operationFactory,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Stdlib\DateTime\DateTime $dateModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Stdlib\String $string,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Io\Ftp $ftpAdapter,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_dateModel = $dateModel;
        $this->_configValueFactory = $configValueFactory;
        $this->_operationFactory = $operationFactory;
        $this->_schedOperFactory = $schedOperFactory;
        $this->_storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->string = $string;
        $this->_transportBuilder = $transportBuilder;
        $this->ftpAdapter = $ftpAdapter;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_init('Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation');
    }

    /**
     * Date model getter
     *
     * @return \Magento\Stdlib\DateTime\DateTime
     */
    public function getDateModel()
    {
        return $this->_dateModel;
    }

    /**
     * Send email notification
     *
     * @param array $vars
     * @return $this
     */
    public function sendEmailNotification($vars = array())
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $copyTo = explode(',', $this->getEmailCopy());
        $copyMethod = $this->getEmailCopyMethod();

        $receiverEmail = $this->_scopeConfig->getValue(
            self::CONFIG_PREFIX_EMAILS . $this->getEmailReceiver() . '/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $receiverName = $this->_scopeConfig->getValue(
            self::CONFIG_PREFIX_EMAILS . $this->getEmailReceiver() . '/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        // Set all required params and send emails
        $this->_transportBuilder->setTemplateIdentifier(
            $this->getEmailTemplate()
        )->setTemplateOptions(
            array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => $storeId)
        )->setTemplateVars(
            $vars
        )->setFrom(
            $this->getEmailSender()
        )->addTo(
            $receiverEmail,
            $receiverName
        );
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $this->_transportBuilder->addBcc($email);
            }
        }
        /** @var \Magento\Mail\TransportInterface $transport */
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $this->_transportBuilder->setTemplateIdentifier(
                    $this->getEmailTemplate()
                )->setTemplateOptions(
                    array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => $storeId)
                )->setTemplateVars(
                    $vars
                )->setFrom(
                    $this->getEmailSender()
                )->addTo(
                    $email
                )->getTransport()->sendMessage();
            }
        }

        return $this;
    }

    /**
     * Unserialize file_info and entity_attributes after load
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    protected function _afterDelete()
    {
        $this->_dropCronTask();
        return parent::_afterDelete();
    }

    /**
     * Add operation to cron
     *
     * @throws \Magento\Framework\Model\Exception
     * @return $this
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
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY ? '1' : '*',
            '*',
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY ? '1' : '*'
        );

        $cronExprString = join(' ', $cronExprArray);
        $exprPath = $this->getExprConfigPath();
        $modelPath = $this->getModelConfigPath();
        try {
            $this->_configValueFactory->create()->load(
                $exprPath,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                $exprPath
            )->save();

            $this->_configValueFactory->create()->load(
                $modelPath,
                'path'
            )->setValue(
                self::CRON_MODEL
            )->setPath(
                $modelPath
            )->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Model\Exception(__('We were unable to save the cron expression.'));
            $this->_logger->logException($e);
        }
        return $this;
    }

    /**
     * Remove cron task
     *
     * @throws \Magento\Framework\Model\Exception
     * @return $this
     */
    protected function _dropCronTask()
    {
        try {
            $this->_configValueFactory->create()->load($this->getExprConfigPath(), 'path')->delete();
            $this->_configValueFactory->create()->load($this->getModelConfigPath(), 'path')->delete();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Model\Exception(__('Unable to delete the cron task.'));
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
     * @param string $jobCode
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function loadByJobCode($jobCode)
    {
        $idPos = strrpos($jobCode, '_');
        if ($idPos !== false) {
            $operationId = (int)substr($jobCode, $idPos + 1);
        }
        if (!isset($operationId) || !$operationId) {
            throw new \Magento\Framework\Model\Exception(__('Please correct the cron job task'));
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

        $logDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::LOG_DIR);
        $filePath = $this->getHistoryFilePath();

        if ($logDirectory->isExist($logDirectory->getRelativePath($filePath))) {
            $filePath = __('File has been not created');
        }

        if (!$result || isset($e) && is_object($e)) {
            $operation->addLogComment(__('Something went wrong and the operation failed.'));
            $this->sendEmailNotification(
                array(
                    'operationName' => $this->getName(),
                    'trace' => nl2br($operation->getFormatedLogTrace()),
                    'entity' => $this->getEntityType(),
                    'dateAndTime' => $runDate,
                    'fileName' => $filePath
                )
            );
        }

        $this->setIsSuccess($result);
        $this->save();

        return $result;
    }

    /**
     * Get file based on "file_info" from server (ftp, local) and put to tmp directory
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation
     * @throws \Magento\Framework\Model\Exception
     * @return string full file path
     */
    public function getFileSource(
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation
    ) {
        $fileInfo = $this->getFileInfo();
        if (empty($fileInfo['file_name']) || empty($fileInfo['file_path'])) {
            throw new \Magento\Framework\Model\Exception(
                __("We couldn't read the file source because the file name is empty.")
            );
        }
        $operation->addLogComment(__('Connecting to server'));
        $operation->addLogComment(__('Reading import file'));

        $extension = pathinfo($fileInfo['file_name'], PATHINFO_EXTENSION);
        $filePath = $fileInfo['file_name'];
        $filePath = trim($fileInfo['file_path'], '\\/') . '/' . $filePath;
        $tmpFile = uniqid() . '.' . $extension;

        try {
            $tmpFilePath = $this->readData($filePath, $tmpFile);
        } catch (\Magento\Framework\Filesystem\FilesystemException $e) {
            throw new \Magento\Framework\Model\Exception(__("We couldn't read the import file."));
        }
        $operation->addLogComment(__('Save history file content "%1"', $this->getHistoryFilePath()));
        $this->_saveOperationHistory($tmpFilePath);
        return $tmpFilePath;
    }

    /**
     * Save/upload file to server (ftp, local)
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation
     * @param string $fileContent
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     */
    public function saveFileSource(
        \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation,
        $fileContent
    ) {
        $operation->addLogComment(__('Save history file content "%1"', $this->getHistoryFilePath()));
        $this->_saveOperationHistory($fileContent);

        $fileInfo = $this->getFileInfo();
        $fileName = $operation->getScheduledFileName() . '.' . $fileInfo['file_format'];
        try {
            $result = $this->writeData($fileInfo['file_path'] . '/' . $fileName, $fileContent);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Model\Exception(
                __(
                    'We couldn\'t write file "%1" to "%2" with the "%3" driver.',
                    $fileName,
                    $fileInfo['file_path'],
                    $fileInfo['server_type']
                )
            );
        }
        $operation->addLogComment(__('Save file content'));

        return $result;
    }

    /**
     * Write data to specific storage (FTP, local filesystem)
     *
     * @param string $filePath
     * @param string $fileContent
     * @return bool|int
     * @throws \Magento\Io\IoException
     * @throws \Magento\Filesystem\FilesystemException
     * @throws \Magento\Model\Exception
     */
    protected function writeData($filePath, $fileContent)
    {
        $this->validateAdapterType();
        $fileInfo = $this->getFileInfo();
        if (Data::FTP_STORAGE == $fileInfo['server_type']) {
            $this->ftpAdapter->open($this->_prepareIoConfiguration($fileInfo));
            $filePath = '/' . trim($filePath, '\\/');
            $result = $this->ftpAdapter->write($filePath, $fileContent);
        } else {
            $varDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::VAR_DIR);
            $result = $varDirectory->writeFile($filePath, $fileContent);
        }

        return $result;
    }

    /**
     * Check if data has 'server_type' and it's valid
     *
     * @return null
     * @throws \Magento\Model\Exception
     */
    protected function validateAdapterType()
    {
        $fileInfo = $this->getFileInfo();
        $availableTypes = $this->_operationFactory->create()->getServerTypesOptionArray();
        if (!isset($fileInfo['server_type'])
            || !$fileInfo['server_type']
            || !isset($availableTypes[$fileInfo['server_type']])
        ) {
            throw new \Magento\Model\Exception(__('Please correct the server type.'));
        }
    }

    /**
     * Read data from specific storage (FTP, local filesystem)
     *
     * @param string $source
     * @param string $destination
     * @return string
     * @throws \Magento\Io\IoException
     * @throws \Magento\Filesystem\FilesystemException
     * @throws \Magento\Model\Exception
     */
    protected function readData($source, $destination)
    {
        $tmpDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::SYS_TMP_DIR);

        $this->validateAdapterType();
        $fileInfo = $this->getFileInfo();
        if (Data::FTP_STORAGE == $fileInfo['server_type']) {
            $this->ftpAdapter->open($this->_prepareIoConfiguration($fileInfo));
            $source = '/' . trim($source, '\\/');
            $result = $this->ftpAdapter->read($source, $tmpDirectory->getAbsolutePath($destination));
        } else {
            $varDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::VAR_DIR);
            if (!$varDirectory->isExist($source)) {
                throw new \Magento\Model\Exception(__('Import path %1 not exists', $source));
            }
            $contents = $varDirectory->readFile($varDirectory->getRelativePath($source));
            $result = $tmpDirectory->writeFile($destination, $contents);
        }
        if (!$result) {
            throw new \Magento\Model\Exception(__('Could\'t read file'));
        }

        return $tmpDirectory->getAbsolutePath($destination);
    }

    /**
     * Get operation instance by operation type and set specific data to it
     * Supported import, export
     *
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
     */
    public function getInstance()
    {
        /** @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface $operation */
        $operation = $this->_schedOperFactory->create(
            'Magento\ScheduledImportExport\Model\\' . $this->string->upperCaseWords($this->getOperationType())
        );

        $operation->initialize($this);
        return $operation;
    }

    /**
     * Prepare data for server io driver initialization
     *
     * @param array $fileInfo
     * @return array Prepared configuration
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
     * @param string $source
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _saveOperationHistory($source)
    {
        $logDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::LOG_DIR);
        $filePath = $logDirectory->getRelativePath($this->getHistoryFilePath());

        try {
            $logDirectory->writeFile($filePath, $source);
        } catch (\Magento\Framework\Filesystem\FilesystemException $e) {
            throw new \Magento\Framework\Model\Exception(__("We couldn't save the file history file."));
        }
        return $this;
    }

    /**
     * Get file path of history operation files
     *
     * @throws \Magento\Framework\Model\Exception
     * @return string
     */
    public function getHistoryFilePath()
    {
        $logDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::LOG_DIR);
        $dirPath = self::LOG_DIRECTORY . date('Y/m/d') . '/' . self::FILE_HISTORY_DIRECTORY;
        $logDirectory->create($dirPath);

        $fileName = join('_', array($this->_getRunTime(), $this->getOperationType(), $this->getEntityType()));

        $fileInfo = $this->getFileInfo();
        if (isset($fileInfo['file_format'])) {
            $extension = $fileInfo['file_format'];
        } elseif (isset($fileInfo['file_name'])) {
            $extension = pathinfo($fileInfo['file_name'], PATHINFO_EXTENSION);
        } else {
            throw new \Magento\Framework\Model\Exception(__('Unknown file format'));
        }

        return $logDirectory->getAbsolutePath($dirPath . $fileName . '.' . $extension);
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
