<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Customer\Edit;

/**
 * Customer giftregistry list block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
class Registrants extends AbstractEdit
{
    /**
     * Scope Selector 'registry/registrant'
     *
     * @var string
     */
    protected $_prefix = 'registrant';

    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct(
            $context,
            $coreData,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $registry,
            $customerSession,
            $attributeConfig,
            $data
        );
    }

    /**
     * Retrieve Max Recipients
     *
     * @return int
     */
    public function getMaxRegistrant()
    {
        return $this->_giftRegistryData->getMaxRegistrant();
    }

    /**
     * Return array of attributes groupped by group
     *
     * @return array
     */
    public function getGroupedRegistrantAttributes()
    {
        return $this->getGroupedAttributes();
    }

    /**
     * Return registrant collection
     *
     * @return \Magento\GiftRegistry\Model\Resource\Person\Collection
     */
    public function getRegistrantList() {
        return $this->getEntity->getRegistrantCollection();
    }

    /**
     * Reorder attributes array  by group
     *
     * @param array $attributes
     * @return array
     */
    protected function _groupAttributes($attributes)
    {
        $grouped = array();
        if (is_array($attributes)) {
            foreach ($attributes as $field => $fdata){
                if (is_array($fdata)) {
                    $grouped[$field] = $fdata;
                    $grouped[$field]['id'] = $this->_getElementId($field);
                    $grouped[$field]['name'] = $this->_getElementName($field);
                }
            }
        }
        return $grouped;
    }

    /**
     * Prepare html element name
     *
     * @param string $code
     * @return string
     */
    protected function _getElementName($code)
    {
        $custom = ($this->isAttributeStatic($code)) ? '' : '[custom]';
        return $this->_prefix . '[${_index_}]'. $custom . '[' . $code . ']';
    }

    /**
     * Prepare html element id
     *
     * @param string $code
     * @return string
     */
    protected function _getElementId($code)
    {
        $custom = ($this->isAttributeStatic($code)) ? '' : 'custom:';
        return $this->_prefix . ':'. $custom . $code . '${_index_}';
    }

    /**
     * Get current registrant info , formatted in php array of JSON data
     *
     * @param int $entityId id of the giftregistry entity
     * @return array
     */
    public function getRegistrantPresets($entityId)
    {
        $data = array();
        $registrantCollection = $this->getEntity()->getRegistrantsCollection();
        foreach ($registrantCollection->getItems() as $registrant) {
            $data[] = $registrant->unserialiseCustom()->getData();
        }
        return $data;
    }
}
