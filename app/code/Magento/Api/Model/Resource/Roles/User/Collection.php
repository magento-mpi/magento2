<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Roles User Resource Collection
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Resource\Roles\User;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Api\Model\User', 'Magento\Api\Model\Resource\User');
    }

    /**
     * Init collection select
     *
     * @return \Magento\Api\Model\Resource\Roles\User\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->where("user_id > 0");

        return $this;
    }
}
