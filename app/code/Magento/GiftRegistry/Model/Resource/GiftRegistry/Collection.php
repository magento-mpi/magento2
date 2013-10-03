<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry data grid collection
 *
 * @category    Enterise
 * @package     Enterpise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model\Resource\GiftRegistry;

class Collection
    extends \Magento\GiftRegistry\Model\Resource\Type\Collection
{
    /**
     * Add sore data for load
     *
     * @return \Magento\GiftRegistry\Model\Resource\GiftRegistry\Collection|\Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreData();
        return $this;
    }
}
