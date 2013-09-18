<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @deprecated after 1.11.2.0
 * Gift registry view block
 */
namespace Magento\GiftRegistry\Block;

class View extends \Magento\GiftRegistry\Block\Customer\Items
{
    /**
     * Return current giftregistry entity
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        return $this->_coreRegistry->registry('current_entity');
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
            return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
        }
        return '';
    }

    /**
     * Retrieve entity country name
     *
     * @param string $countryCode
     * @return string
     */
    public function getCountryName($countryCode)
    {
        if ($countryCode) {
            $country = \Mage::getModel('Magento\Directory\Model\Country')->loadByCode($countryCode);
            return $country->getName();
        }
        return '';
    }

    /**
     * Retrieve comma-separated list of entity registrant roles
     *
     * @param string $attributeCode
     * @param \Magento\GiftRegistry\Model\Type $type
     * @return string
     */
    public function getRegistrantRoles($attributeCode, $type)
    {
        $registrantRoles = $this->getEntity()->getRegistrantRoles();
        if ($registrantRoles) {
            $roles = array();
            foreach ($registrantRoles as $code) {
                $label = $type->getOptionLabel($attributeCode, $code);
                if ($label) {
                    $roles[] = $label;
                }
            }
            if (count($roles)) {
                return implode(', ', $roles);
            }
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
        $type = \Mage::getModel('Magento\GiftRegistry\Model\Type')->load($typeId);

        $attributes = array_merge(
            array(
                'title' => __('Event'),
                'registrants' => __('Recipient')
            ),
            $type->getListedAttributes(),
            array(
                'customer_name' => __('Registry owner'),
                'message' => __('Message')
            )
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
                 case 'event_country' :
                    $attributeValue = $this->getCountryName($this->getEntity()->getEventCountry());
                    break;
                 case 'role' :
                    $attributeValue = $this->getRegistrantRoles($attributeCode, $type);
                    break;
                default :
                    $attributeValue = $this->getEntity()->getDataUsingMethod($attributeCode);
                    break;
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
