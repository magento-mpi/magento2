<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\System\Config\Backend;

class Cron extends \Magento\Core\Model\Config\Value
{
    const CRON_STRING_PATH = 'crontab/default/jobs/paypal_fetch_settlement_reports/schedule/cron_expr';

    const CRON_MODEL_PATH_INTERVAL = 'paypal/fetch_reports/schedule';

    /**
     * @var \Magento\Core\Model\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\Config\ValueFactory $configValueFactory
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\Config\ValueFactory $configValueFactory,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Cron settings after save
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $cronExprString = '';
        $time = explode(
            ',',
            $this->_configValueFactory->create()->load('paypal/fetch_reports/time', 'path')->getValue()
        );

        if ($this->_configValueFactory->create()->load('paypal/fetch_reports/active', 'path')->getValue()) {
            $interval = $this->_configValueFactory->create()->load(self::CRON_MODEL_PATH_INTERVAL, 'path')->getValue();
            $cronExprString = "{$time[1]} {$time[0]} */{$interval} * *";
        }

        $this->_configValueFactory->create()->load(
            self::CRON_STRING_PATH,
            'path'
        )->setValue(
            $cronExprString
        )->setPath(
            self::CRON_STRING_PATH
        )->save();

        return parent::_afterSave();
    }
}
