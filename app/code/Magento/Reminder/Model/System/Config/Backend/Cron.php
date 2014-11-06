<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\System\Config\Backend;

use Magento\Framework\Model\Exception;

/**
 * Reminder Cron Backend Model
 */
class Cron extends \Magento\Framework\App\Config\Value
{
    const CRON_STRING_PATH = 'crontab/default/jobs/send_notification/schedule/cron_expr';

    const CRON_MODEL_PATH = 'crontab/default/jobs/send_notification/run/model';

    /**
     * Configuration Value Factory
     *
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_valueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\ValueFactory $valueFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\ValueFactory $valueFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
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
     * @throws \Magento\Framework\Model\Exception
     */
    public function afterSave()
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
