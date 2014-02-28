<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Resource\Report;

/**
 * Rule report resource model
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rule extends \Magento\Reports\Model\Resource\Report\AbstractReport
{
    /**
     * @var \Magento\SalesRule\Model\Resource\Report\Rule\CreatedatFactory
     */
    protected $_createdatFactory;

    /**
     * @var \Magento\SalesRule\Model\Resource\Report\Rule\UpdatedatFactory
     */
    protected $_updatedatFactory;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Logger $logger
     * @param \Magento\LocaleInterface $locale
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     * @param \Magento\SalesRule\Model\Resource\Report\Rule\CreatedatFactory $createdatFactory
     * @param \Magento\SalesRule\Model\Resource\Report\Rule\UpdatedatFactory $updatedatFactory
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Logger $logger,
        \Magento\LocaleInterface $locale,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Stdlib\DateTime\Timezone\Validator $timezoneValidator,
        \Magento\SalesRule\Model\Resource\Report\Rule\CreatedatFactory $createdatFactory,
        \Magento\SalesRule\Model\Resource\Report\Rule\UpdatedatFactory $updatedatFactory
    ) {
        parent::__construct($resource, $logger, $locale, $reportsFlagFactory, $dateTime, $timezoneValidator);
        $this->_createdatFactory = $createdatFactory;
        $this->_updatedatFactory = $updatedatFactory;
    }

    /**
     * Resource Report Rule constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setResource('salesrule');
    }

    /**
     * Aggregate Coupons data
     *
     * @param mixed|null $from
     * @param mixed|null $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        $this->_createdatFactory->create()->aggregate($from, $to);
        $this->_updatedatFactory->create()->aggregate($from, $to);
        $this->_setFlagData(\Magento\Reports\Model\Flag::REPORT_COUPONS_FLAG_CODE);

        return $this;
    }

    /**
     * Get all unique Rule Names from aggregated coupons usage data
     *
     * @return array
     */
    public function getUniqRulesNamesList()
    {
        $adapter = $this->_getReadAdapter();
        $tableName = $this->getTable('coupon_aggregated');
        $select = $adapter->select()
            ->from(
                $tableName,
                new \Zend_Db_Expr('DISTINCT rule_name')
            )
            ->where('rule_name IS NOT NULL')
            ->where('rule_name <> ?', '')
            ->order('rule_name ASC');

        $rulesNames = $adapter->fetchAll($select);

        $result = array();

        foreach ($rulesNames as $row) {
            $result[] = $row['rule_name'];
        }

        return $result;
    }
}
