<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource\GiftRegistry;

/**
 * Gift registry data grid collection
 *
 * @category    Enterise
 * @package     Enterpise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\GiftRegistry\Model\Resource\Type\Collection
{
    /**
     * Add sore data for load
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreData();
        return $this;
    }
}
