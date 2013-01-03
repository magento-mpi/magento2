<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Statuses option array
 *
 * @category   Enterprise
 * @package    Enterprice_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Model_BasePrice implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return Base Currency Code
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getWebsite()->getBaseCurrencyCode();
    }
}
