<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Resource\Customer;

use Magento\Core\Model\Registry;

/**
 * Resource collection of customers matched by reminder rule
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Customer\Model\Resource\Customer\Collection
{
    /**
     * Core registry
     *
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Object\Copy\Config $fieldsetConfig
     * @param Registry $coreRegistry
     * @param mixed $connection
     * @param string $modelName
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Object\Copy\Config $fieldsetConfig,
        Registry $coreRegistry,
        $connection = null,
        $modelName = self::CUSTOMER_MODEL_NAME
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $fieldsetConfig,
            $connection,
            $modelName
        );
    }

    /**
     * Instantiate select to get matched customers
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $rule = $this->_coreRegistry->registry('current_reminder_rule');
        $select = $this->getSelect();

        $customerTable = $this->getTable('customer_entity');
        $couponTable = $this->getTable('magento_reminder_rule_coupon');
        $logTable = $this->getTable('magento_reminder_rule_log');
        $salesRuleCouponTable = $this->getTable('salesrule_coupon');

        $select->from(array('c' => $couponTable), array('associated_at', 'emails_failed', 'is_active'));
        $select->where('c.rule_id = ?', $rule->getId());

        $select->joinInner(
            array('e' => $customerTable),
            'e.entity_id = c.customer_id',
            array('entity_id', 'email')
        );

        $subSelect = $this->getConnection()->select();
        $subSelect->from(array('g' => $logTable), array(
            'customer_id',
            'rule_id',
            'emails_sent' => new \Zend_Db_Expr('COUNT(log_id)'),
            'last_sent' => new \Zend_Db_Expr('MAX(sent_at)')
        ));

        $subSelect->where('rule_id = ?', $rule->getId());
        $subSelect->group(array('customer_id', 'rule_id'));

        $select->joinLeft(
            array('l' => $subSelect),
            'l.rule_id = c.rule_id AND l.customer_id = c.customer_id',
            array('l.emails_sent', 'l.last_sent')
        );

        $select->joinLeft(
            array('sc' => $salesRuleCouponTable),
            'sc.coupon_id = c.coupon_id',
            array('code', 'usage_limit', 'usage_per_customer')
        );

        $this->_joinFields['associated_at'] = array('table' => 'c', 'field' => 'associated_at');
        $this->_joinFields['emails_failed'] = array('table' => 'c', 'field' => 'emails_failed');
        $this->_joinFields['is_active']     = array('table' => 'c', 'field' => 'is_active');
        $this->_joinFields['code']          = array('table' => 'sc', 'field' => 'code');
        $this->_joinFields['usage_limit']   = array('table' => 'sc', 'field' => 'usage_limit');
        $this->_joinFields['usage_per_customer'] = array('table' => 'sc', 'field' => 'usage_per_customer');
        $this->_joinFields['emails_sent']   = array('table' => 'l', 'field' => 'emails_sent');
        $this->_joinFields['last_sent']     = array('table' => 'l', 'field' => 'last_sent');

        return $this;
    }
}
