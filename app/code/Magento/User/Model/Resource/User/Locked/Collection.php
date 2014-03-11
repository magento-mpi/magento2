<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource\User\Locked;

/**
 * Admin user collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\User\Model\Resource\User\Collection
{
    /**
     * Collection Init Select
     *
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('lock_expires', array('notnull' => true));
    }
}
