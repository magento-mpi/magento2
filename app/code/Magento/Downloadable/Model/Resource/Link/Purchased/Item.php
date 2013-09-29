<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Downloadable Product link purchased items resource model
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Model\Resource\Link\Purchased;

class Item extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Magento class constructor
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable_link_purchased_item', 'item_id');
    }
}
