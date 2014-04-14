<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\System\Config\Backend;

use Magento\Model\Exception;
use Magento\Model\AbstractModel;

/**
 * Reminder Cron Backend Model
 */
class Cron extends \Magento\App\Config\Value
{
    const CRON_STRING_PATH = 'crontab/default/jobs/send_notification/schedule/cron_expr';

    const CRON_MODEL_PATH = 'crontab/default/jobs/send_notification/run/model';

    /**
     * Configuration Value Factory
     *
     * @var \Magento\App\Config\ValueFactory
     */
    protected $_valueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\App\Config\ValueFactory $valueFactory
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\App\Config\ScopeConfigInterface $config,
        \Magento\App\Config\ValueFactory $valueFactory,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        $runModelPath = '',
        array $data = array()
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_valueFactory = $valueFactory;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Cron settings after save
     *
     * @return void
     * @throws Exception
     */
    protected function _afterSave()
    {
        $cronExprString = '';

        if ($this->getFieldsetDataValue('enabled')) {
            $minutely = \Magento\Reminder\Model\Observer::CRON_MINUTELY;
            $hourly = \Magento\Reminder\Model\Observer::CRON_HOURLY;
            $daily = \Magento\Reminder\Model\Observer::CRON_DAILY;

            $frequency = $this->getFieldsetDataValue('frequency');

            if ($frequency == $minutely) {
                $interval = (int)$this->getFieldsetDataValue('interval');
                $cronExprString = "*/{$interval} * * * *";
            } elseif ($frequency == $hourly) {
                $minutes = (int)$this->getFieldsetDataValue('minutes');
                if ($minutes >= 0 && $minutes <= 59) {
                    $cronExprString = "{$minutes} * * * *";
                } else {
                    throw new Exception(__('Please specify a valid number of minute.'));
                }
            } elseif ($frequency == $daily) {
                $time = $this->getFieldsetDataValue('time');
                $timeMinutes = intval($time[1]);
                $timeHours = intval($time[0]);
                $cronExprString = "{$timeMinutes} {$timeHours} * * *";
            }
        }

        try {
            $this->_valueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();

            $this->_valueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();

        } catch (\Exception $e) {
            throw new Exception(__('Unable to save Cron expression'));
        }
    }
}
