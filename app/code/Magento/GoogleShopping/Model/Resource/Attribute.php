<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Attributes resource model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Resource;

class Attribute extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('googleshopping_attributes', 'id');
    }
}
