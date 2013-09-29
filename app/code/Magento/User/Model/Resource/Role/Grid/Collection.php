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
 * Admin role data grid collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Model\Resource\Role\Grid;

class Collection extends \Magento\User\Model\Resource\Role\Collection
{
    /**
     * Prepare select for load
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('role_type', 'G');
        return $this;
    }
}
