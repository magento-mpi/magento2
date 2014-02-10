<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Model for Currency import options
 *
 * @category   Magento
 * @package    Magento_Cron
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cron\Model\Config\Backend;

class Sitemap extends \Magento\Core\Model\Config\Value
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/sitemap_generate/schedule/cron_expr';

    /**
     * Cron mode path
     */
    const CRON_MODEL_PATH  = 'crontab/default/jobs/sitemap_generate/run/model';

    /**
     * @var \Magento\Core\Model\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\Config\ValueFactory $configValueFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\Config\ValueFactory $configValueFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        $runModelPath = '',
        array $data = array()
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * @return \Magento\Core\Model\AbstractModel
     * @throws \Exception
     */
    protected function _afterSave()
    {
        $time = $this->getData('groups/generate/fields/time/value');
        $frequency = $this->getData('groups/generate/frequency/value');

        $cronExprArray = array(
            intval($time[1]), //Minute
            intval($time[0]), //Hour
            ($frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY) ? '1' : '*', //Day of the Month
            '*', //Month of the Year
            ($frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY) ? '1' : '*', //# Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->_configValueFactory->create()
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            $this->_configValueFactory->create()
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue($this->_runModelPath)
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (\Exception $e) {
            throw new \Exception(__('We can\'t save the cron expression.'));
        }
    }
}
