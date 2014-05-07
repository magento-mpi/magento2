<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Resource\Link;

/**
 * Downloadable Product link purchased resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Purchased extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Magento class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('downloadable_link_purchased', 'purchased_id');
    }
}
