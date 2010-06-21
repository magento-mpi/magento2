<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry view block
 */
class Enterprise_GiftRegistry_Block_View extends Enterprise_GiftRegistry_Block_Customer_Items
{
    /**
     * Return current giftregistry entity
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return Mage::registry('current_entity');
    }

    /**
     * Retrieve entity formated date
     *
     * @return string
     */
    public function getFormattedDate($item)
    {
        return $this->formatDate($item->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Retrieve item remaining qty
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getItemQtyRemaining($item)
    {
        return ($this->getItemQty($item) - $this->getItemQtyFulfilled($item)) * 1;
    }

    /**
     * Retrieve attributes to display info array
     *
     * @return array
     */
    public function getAttributesToDisplay()
    {
        $attributes = array(
            'title'          => $this->__('Event'),
            'registrants'    => $this->__('Recipient'),
            'created_at'     => $this->__('Event Date'),
            'event_location' => $this->__('Location'),
            'customer_name'  => $this->__('Registry owner'),
            'message'        => $this->__('Message'),
        );
        $result = array();
        foreach ($attributes as $attributeCode => $attributeTitle) {
            if ($attributeCode == 'customer_name') {
                $attributeValue = $this->getEntity()->getCustomer()->getName();
            } else {
                $attributeValue = $this->getEntity()->getDataUsingMethod($attributeCode);
            }
            if ((string)$attributeValue == '') {
                continue;
            }
            if ($attributeCode == 'created_at') {
                $attributeValue = $this->getFormattedDate($this->getEntity());
            }
            $attributeValue = $this->escapeHtml($attributeValue);
            $result[] = array(
                'title' => $attributeTitle,
                'value' => $attributeValue
            );
        }
        return $result;
    }
}
