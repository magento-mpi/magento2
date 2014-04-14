<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Model\Config\Backend;

/**
 * Backup by cron backend model
 */
class Cron extends \Magento\Framework\App\Config\Value
{
    const CRON_STRING_PATH = 'crontab/default/jobs/system_backup/schedule/cron_expr';

    const CRON_MODEL_PATH = 'crontab/default/jobs/system_backup/run/model';

    const XML_PATH_BACKUP_ENABLED = 'groups/backup/fields/enabled/value';

    const XML_PATH_BACKUP_TIME = 'groups/backup/fields/time/value';

    const XML_PATH_BACKUP_FREQUENCY = 'groups/backup/fields/frequency/value';

    /**
     * Config value factory
     *
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        $runModelPath = '',
        array $data = array()
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Cron settings after save
     *
     * @return void
     * @throws \Magento\Model\Exception
     */
    protected function _afterSave()
    {
        $enabled = $this->getData(self::XML_PATH_BACKUP_ENABLED);
        $time = $this->getData(self::XML_PATH_BACKUP_TIME);
        $frequency = $this->getData(self::XML_PATH_BACKUP_FREQUENCY);

        $frequencyWeekly = \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY;
        $frequencyMonthly = \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),                                 # Minute
                intval($time[0]),                                 # Hour
                $frequency == $frequencyMonthly ? '1' : '*',      # Day of the Month
                '*',                                              # Month of the Year
                $frequency == $frequencyWeekly ? '1' : '*'        # Day of the Week
            );
            $cronExprString = join(' ', $cronExprArray);
        } else {
            $cronExprString = '';
        }

        try {
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();

            $this->_configValueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();
        } catch (\Exception $e) {
            throw new \Magento\Model\Exception(__('We can\'t save the Cron expression.'));
        }
    }
}
