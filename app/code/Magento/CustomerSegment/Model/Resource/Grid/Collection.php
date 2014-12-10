<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Customer segment data grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\Resource\Grid;

class Collection extends \Magento\CustomerSegment\Model\Resource\Segment\Collection
{
    /**
     * Add websites for load
     *
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection|\Magento\CustomerSegment\Model\Resource\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
