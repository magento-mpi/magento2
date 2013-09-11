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
 * Downloadable links purchased items resource collection
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Model\Resource\Link\Purchased\Item;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Downloadable\Model\Link\Purchased\Item', '\Magento\Downloadable\Model\Resource\Link\Purchased\Item');
    }
}
