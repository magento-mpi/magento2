<?php
/**
 * Cms block grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Block\Grid;

class Collection extends \Magento\Cms\Model\Resource\Block\Collection
{

    /**
     * @return \Magento\Cms\Model\Resource\Block\Grid\Collection
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
        parent::_afterLoad();
    }

    /**
     * @param string $field
     * @param null $condition
     * @return \Magento\Cms\Model\Resource\Block\Grid\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_id'){
            return $this->addStoreFilter($field);
        }
    }
}
