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
     * @param string $date
     * @return string
     */
    public function getFormattedDate($date)
    {
        if ($date) {
            return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        }
        return '';
    }

    /**
     * Retrieve attributes to display info array
     *
     * @return array
     */
    public function getAttributesToDisplay()
    {
        $typeId = $this->getEntity()->getTypeId();
        $type = Mage::getModel('enterprise_giftregistry/type')->load($typeId);

        $attributes = array(
            'title'          => $this->__('Event'),
            'registrants'    => $this->__('Recipient'),
            'event_date'     => $type->getAttributeLabel('event_date'),
            'event_location' => $type->getAttributeLabel('event_location'),
            'customer_name'  => $this->__('Registry owner'),
            'message'        => $this->__('Message'),
        );

        $result = array();
        foreach ($attributes as $attributeCode => $attributeTitle) {
            switch($attributeCode) {
                case 'customer_name' :
                    $attributeValue = $this->getEntity()->getCustomer()->getName();
                    break;
                case 'event_date' :
                    $attributeValue = $this->getFormattedDate($this->getEntity()->getEventDate());
                    break;
                default :
                    $attributeValue = $this->getEntity()->getDataUsingMethod($attributeCode);
            }

            if ((string)$attributeValue == '') {
                continue;
            }
            $result[] = array(
                'title' => $attributeTitle,
                'value' => $this->escapeHtml($attributeValue)
            );
        }
        return $result;
    }
}
