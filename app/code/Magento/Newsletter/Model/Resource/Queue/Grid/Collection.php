<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter queue data grid collection
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Model\Resource\Queue\Grid;

class Collection
    extends \Magento\Newsletter\Model\Resource\Queue\Collection
{
    /**
     * @return \Magento\Newsletter\Model\Resource\Queue\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscribersInfo();
        return $this;
    }
}
