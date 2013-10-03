<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Collection of banner <-> sales rule associations
 */
namespace Magento\Banner\Model\Resource\Salesrule;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'magento_banner_salesrule_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * Define collection item type and corresponding table
     */
    protected function _construct()
    {
        $this->_init('Magento\Object', 'Magento\SalesRule\Model\Resource\Rule');
        $this->setMainTable('magento_banner_salesrule');
    }

    /**
     * Filter out disabled banners
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(
                array('banner' => $this->getTable('magento_banner')),
                'banner.banner_id = main_table.banner_id AND banner.is_enabled = 1',
                array()
            )
            ->group('main_table.banner_id')
        ;
        return $this;
    }

    /**
     * Add sales rule ids filter to the collection
     *
     * @param array $ruleIds
     * @return \Magento\Banner\Model\Resource\Salesrule\Collection
     */
    public function addRuleIdsFilter(array $ruleIds)
    {
        if (!$ruleIds) {
            // force to match no rules
            $ruleIds = array(0);
        }
        $this->addFieldToFilter('main_table.rule_id', array('in' => $ruleIds));
        return $this;
    }
}
