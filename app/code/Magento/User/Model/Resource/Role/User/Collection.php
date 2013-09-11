<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin role users collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Model\Resource\Role\User;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\User\Model\User', 'Magento\User\Model\Resource\User');
    }

    /**
     * Initialize select
     *
     * @return \Magento\User\Model\Resource\Role\User\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->where("user_id > 0");

        return $this;
    }
}
