<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Resource\Reminder;

/**
 * Reminder data grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\Reminder\Model\Resource\Rule\Collection
{
    /**
     * Initialize reminder rule collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
