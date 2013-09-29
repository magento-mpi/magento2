<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reminder data grid collection
 *
 * @category    Enterise
 * @package     Enterpise_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reminder\Model\Resource\Reminder;

class Collection
    extends \Magento\Reminder\Model\Resource\Rule\Collection
{
    /**
     * @return \Magento\Reminder\Model\Resource\Reminder\Collection|\Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
