<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sendfriend log resource collection
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sendfriend\Model\Resource\Sendfriend;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Init resource collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Sendfriend\Model\Sendfriend', 'Magento\Sendfriend\Model\Resource\Sendfriend');
    }
}
