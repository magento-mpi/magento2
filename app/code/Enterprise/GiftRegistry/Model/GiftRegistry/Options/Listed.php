<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Statuses option array
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_GiftRegistry_Options_Listed implements Mage_Core_Model_Option_ArrayInterface
{

    /**
     * @var Enterprise_GiftRegistry_Helper_Data
     */
    protected $_helper;

    /**
     * @param Enterprise_GiftRegistry_Helper_Data
     */
    public function __construct(Enterprise_GiftRegistry_Helper_Data $giftRegistryHelper)
    {
        $this->_helper = $giftRegistryHelper;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            1 => __('Yes'),
            0 => __('No'),
        );
    }
}
