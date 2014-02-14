<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource\User;

/**
 * Admin user collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\User\Model\User', 'Magento\User\Model\Resource\User');
    }
}
