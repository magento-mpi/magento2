<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup by cron backend model
 */
namespace Magento\Backup\Model\Config\Backend;

class Cron extends \Magento\Core\Model\Config\Value
{
    const CRON_STRING_PATH  = 'crontab/jobs/system_backup/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/system_backup/run/model';

    const XML_PATH_BACKUP_ENABLED       = 'groups/backup/fields/enabled/value';
    const XML_PATH_BACKUP_TIME          = 'groups/backup/fields/time/value';
    const XML_PATH_BACKUP_FREQUENCY     = 'groups/backup/fields/frequency/value';

    /**
     * Config value factory
     *
     * @var \Magento\Core\Model\Config\Value
     */
    protected $_configValueFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Config\ValueFactory $configValueFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Config\ValueFactory $configValueFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);

        $this->_configValueFactory = $configValueFactory;
    }

    /**
     * Cron settings after save
     *
     * @return \Magento\Backend\Model\Config\Backend\Log\Cron
     * @throws \Magento\Core\Exception
     */
    protected function _afterSave()
    {
        $enabled   = $this->getData(self::XML_PATH_BACKUP_ENABLED);
        $time      = $this->getData(self::XML_PATH_BACKUP_TIME);
        $frequency = $this->getData(self::XML_PATH_BACKUP_FREQUENCY);

        $frequencyWeekly  = \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY;
        $frequencyMonthly = \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
                '*',                                                # Month of the Year
                ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
            );
            $cronExprString = join(' ', $cronExprArray);
        } else {
            $cronExprString = '';
        }

        try {
            $this->_configValueFactory->create()
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            $this->_configValueFactory->create()
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) $this->_coreConfig->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (\Exception $e) {
            throw new \Magento\Core\Exception(__('We can\'t save the Cron expression.'));
        }
    }
}
