<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Resource;

use Magento\Framework\Model\AbstractModel;
use Magento\Reminder\Model\Rule as ModelRule;
use Magento\SalesRule\Model\Rule as SalesRule;

/**
 * Reminder Rule resource model
 */
class Rule extends \Magento\Rule\Model\Resource\AbstractResource
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'website' => [
            'associations_table' => 'magento_reminder_rule_website',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'website_id',
        ],
    ];

    /**
     * Rule websites table name
     *
     * @var string
     *
     * @deprecated after 1.11.2.0
     */
    protected $_websiteTable;

    /**
     * Core resource helper
     *
     * @var \Magento\Framework\DB\Helper
     */
    protected $_resourceHelper;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        parent::__construct($resource);
        $this->_resourceHelper = $resourceHelper;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_reminder_rule', 'rule_id');
        $this->_websiteTable = $this->getTable('magento_reminder_rule_website');
    }

    /**
     * Add website ids to rule data after load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Bind reminder rule to and website(s).  Save store templates data.
     *
     * @param AbstractModel $rule
     * @return $this
     */
    protected function _afterSave(AbstractModel $rule)
    {
        if ($rule->hasWebsiteIds()) {
            $websiteIds = $rule->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($rule->getId(), $websiteIds, 'website');
        }

        if ($rule->hasData('store_templates')) {
            $this->_saveStoreData($rule);
        }

        parent::_afterSave($rule);
        return $this;
    }

    /**
     * Save store templates
     *
     * @param ModelRule $rule
     * @return $this
     * @throws \Exception
     */
    protected function _saveStoreData($rule)
    {
        $adapter = $this->_getWriteAdapter();
        $templateTable = $this->getTable('magento_reminder_template');
        $labels = (array)$rule->getStoreLabels();
        $descriptions = (array)$rule->getStoreDescriptions();
        $templates = (array)$rule->getStoreTemplates();
        $ruleId = $rule->getId();

        $data = [];
        foreach ($templates as $storeId => $templateId) {
            if (!$templateId) {
                continue;
            }
            if (!is_numeric($templateId)) {
                $templateId = null;
            }
            $data[] = [
                'rule_id' => $ruleId,
                'store_id' => $storeId,
                'template_id' => $templateId,
                'label' => isset($labels[$storeId]) ? $labels[$storeId] : '',
                'description' => isset($descriptions[$storeId]) ? $descriptions[$storeId] : '',
            ];
        }

        $adapter->beginTransaction();
        try {
            $adapter->delete($templateTable, ['rule_id=?' => $ruleId]);
            if (!empty($data)) {
                $adapter->insertMultiple($templateTable, $data);
            }
        } catch (\Exception $e) {
            $adapter->rollback();
            throw $e;
        }
        $adapter->commit();
        return $this;
    }

    /**
     * Get store templates data assigned to reminder rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreData($ruleId)
    {
        $templateTable = $this->getTable('magento_reminder_template');
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()->from(
            $templateTable,
            ['store_id', 'template_id', 'label', 'description']
        )->where(
            'rule_id = :rule_id'
        );
        return $adapter->fetchAll($select, ['rule_id' => $ruleId]);
    }

    /**
     * Get store templates data (labels and descriptions) assigned to reminder rule.
     *
     * If labels and descriptions are not specified it will be replaced with default values.
     *
     * @param int $ruleId
     * @param int $storeId
     * @return array
     */
    public function getStoreTemplateData($ruleId, $storeId)
    {
        $templateTable = $this->getTable('magento_reminder_template');
        $ruleTable = $this->getTable('magento_reminder_rule');
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()->from(
            ['t' => $templateTable],
            [
                'template_id',
                'label' => $adapter->getCheckSql('t.label IS NOT NULL', 't.label', 'r.default_label'),
                'description' => $adapter->getCheckSql(
                    't.description IS NOT NULL',
                    't.description',
                    'r.default_description'
                )
            ]
        )->join(
            ['r' => $ruleTable],
            'r.rule_id = t.rule_id',
            []
        );

        $select->where('t.rule_id = :rule_id');
        $select->where('t.store_id = :store_id');

        return $adapter->fetchRow($select, ['rule_id' => $ruleId, 'store_id' => $storeId]);
    }

    /**
     * Deactivate already matched customers before new matching process
     *
     * @param int $ruleId
     * @return $this
     */
    public function deactivateMatchedCustomers($ruleId)
    {
        $this->_getWriteAdapter()->update(
            $this->getTable('magento_reminder_rule_coupon'),
            ['is_active' => '0'],
            ['rule_id = ?' => $ruleId]
        );
        return $this;
    }

    /**
     * Try to associate reminder rule with matched customers.
     *
     * If customer was added earlier, update is_active column.
     *
     * @param ModelRule $rule
     * @param SalesRule $salesRule
     * @param int $websiteId
     * @param int $threshold
     * @return $this
     * @throws \Exception
     */
    public function saveMatchedCustomers($rule, $salesRule, $websiteId, $threshold = null)
    {
        $rule->afterLoad();
        /** @var $select \Zend_Db_Select */
        $select = $rule->getConditions()->getConditionsSql(null, $websiteId);

        if (!$rule->getConditionSql()) {
            return $this;
        }

        if ($threshold) {
            $select->where('c.emails_failed IS NULL OR c.emails_failed < ? ', $threshold);
        }

        $ruleId = $rule->getId();
        $adapter = $this->_getWriteAdapter();
        $couponsTable = $this->getTable('magento_reminder_rule_coupon');
        $currentDate = $this->dateTime->formatDate(time());
        $dataToInsert = [];

        $stmt = $adapter->query($select, ['rule_id' => $ruleId]);

        $adapter->beginTransaction();
        try {
            $i = 0;
            while (true == ($row = $stmt->fetch())) {
                if (empty($row['coupon_id']) && $salesRule) {
                    $coupon = $salesRule->acquireCoupon();
                    $couponId = $coupon !== null ? $coupon->getId() : null;
                } else {
                    $couponId = $row['coupon_id'];
                }

                $dataToInsert[] = [
                    'rule_id' => $ruleId,
                    'coupon_id' => $couponId,
                    'customer_id' => $row['entity_id'],
                    'associated_at' => $currentDate,
                    'is_active' => '1',
                ];
                $i++;

                if ($i % 1000 == 0) {
                    $adapter->insertOnDuplicate($couponsTable, $dataToInsert, ['is_active']);
                    $dataToInsert = [];
                }
            }
            if (!empty($dataToInsert)) {
                $adapter->insertOnDuplicate($couponsTable, $dataToInsert, ['is_active']);
            }
        } catch (\Exception $e) {
            $adapter->rollBack();
            throw $e;
        }
        $adapter->commit();
        return $this;
    }

    /**
     * Retrieve list of customers for notification process.
     *
     * This process can be initialized by system cron or by admin for particular rule
     *
     * @param int|null $limit
     * @param int|null $ruleId
     * @return array
     */
    public function getCustomersForNotification($limit = null, $ruleId = null)
    {
        $couponTable = $this->getTable('magento_reminder_rule_coupon');
        $ruleTable = $this->getTable('magento_reminder_rule');
        $logTable = $this->getTable('magento_reminder_rule_log');
        $adapter = $this->_getReadAdapter();
        $currentDate = $this->dateTime->formatDate(time());

        $select = $adapter->select()->from(
            ['c' => $couponTable],
            ['customer_id', 'coupon_id', 'rule_id']
        )->join(
            ['r' => $ruleTable],
            'c.rule_id = r.rule_id AND r.is_active = 1',
            ['schedule' => 'schedule']
        )->joinLeft(
            ['l' => $logTable],
            'c.rule_id = l.rule_id AND c.customer_id = l.customer_id',
            []
        );

        if ($ruleId) {
            $select->where('c.rule_id = ?', $ruleId);
        }

        $select->where('c.is_active = 1');
        $select->group(['c.customer_id', 'c.rule_id']);
        $select->columns(['log_sent_at_max' => 'MAX(l.sent_at)', 'log_sent_at_min' => 'MIN(l.sent_at)']);

        $findInSetSql = $adapter->prepareSqlCondition(
            'schedule',
            [
                'finset' => $this->_resourceHelper->getDateDiff('log_sent_at_min', $adapter->formatDate($currentDate))
            ]
        );
        $select->having(
            'log_sent_at_max IS NULL OR (' . $findInSetSql . ' AND ' . $this->_resourceHelper->getDateDiff(
                'log_sent_at_max',
                $adapter->formatDate($currentDate)
            ) . ' > 0)'
        );

        if ($limit) {
            $select->limit($limit);
        }
        return $adapter->fetchAll($select);
    }

    /**
     * Add notification log row after letter was successfully sent.
     *
     * @param int $ruleId
     * @param int $customerId
     * @return $this
     */
    public function addNotificationLog($ruleId, $customerId)
    {
        $data = [
            'rule_id' => $ruleId,
            'customer_id' => $customerId,
            'sent_at' => $this->dateTime->formatDate(time()),
        ];

        $this->_getWriteAdapter()->insert($this->getTable('magento_reminder_rule_log'), $data);

        return $this;
    }

    /**
     * Update failed email counter.
     *
     * @param int $ruleId
     * @param int $customerId
     * @return $this
     */
    public function updateFailedEmailsCounter($ruleId, $customerId)
    {
        $this->_getWriteAdapter()->update(
            $this->getTable('magento_reminder_rule_coupon'),
            ['emails_failed' => new \Zend_Db_Expr('emails_failed + 1')],
            ['rule_id = ?' => $ruleId, 'customer_id = ?' => $customerId]
        );
        return $this;
    }

    /**
     * Retrieve count of reminder rules assigned to specified sales rule.
     *
     * @param int $salesRuleId
     * @return string
     */
    public function getAssignedRulesCount($salesRuleId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            ['r' => $this->getTable('magento_reminder_rule')],
            [new \Zend_Db_Expr('count(1)')]
        );
        $select->where('r.salesrule_id = :salesrule_id');

        return $adapter->fetchOne($select, ['salesrule_id' => $salesRuleId]);
    }

    /**
     * Detaches sales rule from all Email Remainder Rules that uses it
     *
     * @param int $salesRuleId
     * @return $this
     */
    public function detachSalesRule($salesRuleId)
    {
        $this->_getWriteAdapter()->update(
            $this->getTable('magento_reminder_rule'),
            ['salesrule_id' => new \Zend_Db_Expr('NULL')],
            ['salesrule_id = ?' => $salesRuleId]
        );

        return $this;
    }

    /**
     * Get comparison condition for rule condition operator which will be used in SQL query
     *
     * @param string $operator
     * @return string
     * @throws \Magento\Framework\Model\Exception
     */
    public function getSqlOperator($operator)
    {
        switch ($operator) {
            case '==':
                return '=';
            case '!=':
                return '<>';
            case '{}':
                return 'LIKE';
            case '!{}':
                return 'NOT LIKE';
            case 'between':
                return 'BETWEEN %s AND %s';
            case '>':
            case '<':
            case '>=':
            case '<=':
                return $operator;
            default:
                throw new \Magento\Framework\Model\Exception(__('Unknown operator specified.'));
        }
    }

    /**
     * Create string for select "where" condition based on field name, comparison operator and vield value
     *
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    public function createConditionSql($field, $operator, $value)
    {
        $sqlOperator = $this->getSqlOperator($operator);
        $adapter = $this->_getReadAdapter();

        $condition = '';
        switch ($operator) {
            case '{}':
            case '!{}':
                if (is_array($value)) {
                    if (!empty($value)) {
                        $sqlOperator = $operator == '{}' ? 'IN' : 'NOT IN';
                        $condition = $adapter->quoteInto($field . ' ' . $sqlOperator . ' (?)', $value);
                    }
                } else {
                    $condition = $adapter->quoteInto($field . ' ' . $sqlOperator . ' ?', '%' . $value . '%');
                }
                break;
            case 'between':
                $condition = $field . ' ' . sprintf(
                    $sqlOperator,
                    $adapter->quote($value['start']),
                    $adapter->quote($value['end'])
                );
                break;
            default:
                $condition = $adapter->quoteInto($field . ' ' . $sqlOperator . ' ?', $value);
                break;
        }

        return $condition;
    }

    /**
     * Quote parameters into condition string
     *
     * @param string $string
     * @param string|array $param
     * @return string
     *
     * @deprecated since 1.10.0.0 - please use quoteInto of current adapter
     */
    public function quoteInto($string, $param)
    {
        return $this->_getReadAdapter()->quoteInto($string, $param);
    }

    /**
     * Save customer data by matched customer coupons
     *
     * @param array $data
     * @return void
     *
     * @deprecated after 1.11.2.0
     */
    protected function _saveMatchedCustomerData($data)
    {
        if ($data) {
            $table = $this->getTable('magento_reminder_rule_coupon');
            $this->_getWriteAdapter()->insertOnDuplicate($table, $data, ['is_active']);
        }
    }

    /**
     * Save all website ids associated to rule
     *
     * @param \Magento\Reminder\Model\Rule $rule
     * @return $this
     *
     * @deprecated after 1.11.2.0 use $this->bindRuleToEntity() instead
     */
    protected function _saveWebsiteIds($rule)
    {
        if ($rule->hasWebsiteIds()) {
            $websiteIds = $rule->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($rule->getId(), $websiteIds, 'website');
        }

        return $this;
    }

    /**
     * Get empty select object
     *
     * @return \Magento\Framework\DB\Select
     *
     * @deprecated after 1.11.2.0
     */
    public function createSelect()
    {
        return $this->_getReadAdapter()->select();
    }
}
