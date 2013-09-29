<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer segment data grid collection
 *
 * @category    Enterise
 * @package     Enterpise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\Resource\Grid;

class Collection
    extends \Magento\CustomerSegment\Model\Resource\Segment\Collection
{
    /**
     * Add websites for load
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\CustomerSegment\Model\Resource\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
