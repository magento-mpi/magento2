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
class Magento_GiftRegistry_Block_View extends Magento_GiftRegistry_Block_Customer_Items
{
    /**
     * @var Magento_Directory_Model_CountryFactory
     */
    protected $countryFactory;

    /**
     * @var Magento_GiftRegistry_Model_TypeFactory
     */
    protected $typeFactory;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_GiftRegistry_Model_ItemFactory $itemFactory
     * @param Magento_Directory_Model_CountryFactory $countryFactory
     * @param Magento_GiftRegistry_Model_TypeFactory $typeFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_GiftRegistry_Model_ItemFactory $itemFactory,
        Magento_Directory_Model_CountryFactory $countryFactory,
        Magento_GiftRegistry_Model_TypeFactory $typeFactory,
        array $data = array()
    ) {
        $this->countryFactory = $countryFactory;
        $this->typeFactory = $typeFactory;
        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $itemFactory, $data);
    }

    /**
     * Return current gift registry entity
     *
     * @return Magento_GiftRegistry_Model_Entity
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
            return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
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
            $country = $this->countryFactory->create()->loadByCode($countryCode);
            return $country->getName();
        }
        return '';
    }

    /**
     * Retrieve comma-separated list of entity registrant roles
     *
     * @param string $attributeCode
     * @param Magento_GiftRegistry_Model_Type $type
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
        $type = $this->typeFactory->create()->load($typeId);

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
