<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter problems collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Model\Resource\Grid;

class Collection extends \Magento\Newsletter\Model\Resource\Problem\Collection
{
    /**
     * Adds queue info to grid
     *
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection|\Magento\Newsletter\Model\Resource\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscriberInfo()->addQueueInfo();
        return $this;
    }
}
