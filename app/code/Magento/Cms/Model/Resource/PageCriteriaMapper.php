<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

use Magento\Framework\DB\GenericMapper;

/**
 * Class PageCriteriaMapper
 */
class PageCriteriaMapper extends GenericMapper
{
    /**
     * @inheritdoc
     */
    protected function init()
    {
        $this->initResource('Magento\Cms\Model\Resource\Page');
        $this->map['fields']['store'] = 'store_table.store_id';
        $this->map['fields']['store_id'] = 'store_table.store_id';
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return void
     */
    public function mapFirstStoreFlag($flag)
    {
        // do nothing since handled in collection afterLoad
    }

    /**
     * Add filter by store
     *
     * @param int|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return void
     */
    public function mapStoreFilter($store, $withAdmin)
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable('cms_page_store')],
            'main_table.page_id = store_table.page_id',
            []
        )->group('main_table.page_id');
        if (!is_array($store)) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $store = [$store->getId()];
            } else {
                $store = [$store];
            }
        }
        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }
        $field = $this->getMappedField('store');
        $this->select->where(
            $this->getConditionSql($field, ['in' => $store]),
            null,
            \Magento\Framework\DB\Select::TYPE_CONDITION
        );
    }
}
