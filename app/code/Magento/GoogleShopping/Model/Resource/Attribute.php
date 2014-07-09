<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Attributes resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Resource;

class Attribute extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('googleshopping_attributes', 'id');
    }
}
