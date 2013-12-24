<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax report resource model
 */
namespace Magento\Tax\Model\Resource\Report;

class Tax extends \Magento\Reports\Model\Resource\Report\AbstractReport
{
    /**
     * @var \Magento\Tax\Model\Resource\Report\Tax\CreatedatFactory
     */
    protected $_createdAtFactory;

    /**
     * @var \Magento\Tax\Model\Resource\Report\Tax\UpdatedatFactory
     */
    protected $_updatedAtFactory;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Tax\Model\Resource\Report\Tax\CreatedatFactory $createdAtFactory
     * @param \Magento\Tax\Model\Resource\Report\Tax\UpdatedatFactory $updatedAtFactory
     * @param \Magento\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Logger $logger,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Tax\Model\Resource\Report\Tax\CreatedatFactory $createdAtFactory,
        \Magento\Tax\Model\Resource\Report\Tax\UpdatedatFactory $updatedAtFactory,
        \Magento\Stdlib\DateTime\Timezone\Validator $timezoneValidator
    ) {
        $this->_createdAtFactory = $createdAtFactory;
        $this->_updatedAtFactory = $updatedAtFactory;
        parent::__construct($resource, $logger, $locale, $reportsFlagFactory, $dateTime, $timezoneValidator);
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Tax data
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Tax\Model\Resource\Report\Tax
     */
    public function aggregate($from = null, $to = null)
    {
        /** @var $createdAt \Magento\Tax\Model\Resource\Report\Tax\Createdat */
        $createdAt = $this->_createdAtFactory->create();
        /** @var $updatedAt \Magento\Tax\Model\Resource\Report\Tax\Updatedat */
        $updatedAt = $this->_updatedAtFactory->create();

        $createdAt->aggregate($from, $to);
        $updatedAt->aggregate($from, $to);
        $this->_setFlagData(\Magento\Reports\Model\Flag::REPORT_TAX_FLAG_CODE);

        return $this;
    }
}
