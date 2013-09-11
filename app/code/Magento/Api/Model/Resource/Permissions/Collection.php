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
 * Permissions Resource Collection
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Resource\Permissions;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Api\Model\Rules', 'Magento\Api\Model\Resource\Rules');
    }
}
