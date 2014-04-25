<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block;

/**
 * @deprecated after 1.11.2.0
 * Gift registry view block
 */
class View extends \Magento\GiftRegistry\Block\Customer\Items
{
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\GiftRegistry\Model\TypeFactory
     */
    protected $typeFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\GiftRegistry\Model\TypeFactory $typeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\GiftRegistry\Model\TypeFactory $typeFactory,
        array $data = array()
    ) {
        $this->countryFactory = $countryFactory;
        $this->typeFactory = $typeFactory;
        parent::__construct(
            $context,
            $coreData,
            $itemFactory,
            $data
        );
    }

    /**
     * Return current gift registry entity
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
            return $this->formatDate($date, \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM);
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
        $type = $this->typeFactory->create()->load($typeId);

        $attributes = array_merge(
            array('title' => __('Event'), 'registrants' => __('Recipient')),
            $type->getListedAttributes(),
            array('customer_name' => __('Registry owner'), 'message' => __('Message'))
        );

        $result = array();
        foreach ($attributes as $attributeCode => $attributeTitle) {
            switch ($attributeCode) {
                case 'customer_name':
                    $attributeValue = $this->getEntity()->getCustomer()->getName();
                    break;
                case 'event_date':
                    $attributeValue = $this->getFormattedDate($this->getEntity()->getEventDate());
                    break;
                case 'event_country':
                    $attributeValue = $this->getCountryName($this->getEntity()->getEventCountry());
                    break;
                case 'role':
                    $attributeValue = $this->getRegistrantRoles($attributeCode, $type);
                    break;
                default:
                    $attributeValue = $this->getEntity()->getDataUsingMethod($attributeCode);
                    break;
            }

            if ((string)$attributeValue == '') {
                continue;
            }
            $result[] = array('title' => $attributeTitle, 'value' => $this->escapeHtml($attributeValue));
        }
        return $result;
    }
}
