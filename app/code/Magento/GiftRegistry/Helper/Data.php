<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Helper;

use Magento\Catalog\Model\Product;
use Magento\Sales\Model\Quote\Item;

/**
 * Gift Registry helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'magento_giftregistry/general/enabled';

    const XML_PATH_SEND_LIMIT = 'magento_giftregistry/sharing_email/send_limit';

    const XML_PATH_MAX_REGISTRANT = 'magento_giftregistry/general/max_registrant';

    const ADDRESS_PREFIX = 'gr_address_';

    /**
     * Option for address source selector
     * @var string
     */
    const ADDRESS_NEW = 'new';

    /**
     * Option for address source selector
     * @var string
     */
    const ADDRESS_NONE = 'none';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\GiftRegistry\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\UrlFactory
     */
    protected $urlFactory;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\EntityFactory $entityFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\UrlFactory $urlFactory
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Escaper $escaper
     * @param \Magento\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\EntityFactory $entityFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\UrlFactory $urlFactory,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Escaper $escaper,
        \Magento\Locale\ResolverInterface $localeResolver
    ) {
        parent::__construct($context);
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->customerSession = $customerSession;
        $this->entityFactory = $entityFactory;
        $this->productFactory = $productFactory;
        $this->urlFactory = $urlFactory;
        $this->_localeDate = $localeDate;
        $this->_escaper = $escaper;
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Check whether gift registry is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_coreStoreConfig->getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Retrieve sharing recipients limit config data
     *
     * @return int
     */
    public function getRecipientsLimit()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_SEND_LIMIT);
    }

    /**
     * Retrieve address prefix
     *
     * @return int
     */
    public function getAddressIdPrefix()
    {
        return self::ADDRESS_PREFIX;
    }

    /**
     * Retrieve Max Recipients
     *
     * @param int $store
     * @return int
     */
    public function getMaxRegistrant($store = null)
    {
        return (int)$this->_coreStoreConfig->getConfig(self::XML_PATH_MAX_REGISTRANT, $store);
    }

    /**
     * Validate custom attributes values
     *
     * @param array $customValues
     * @param array $attributes
     * @return array|bool
     */
    public function validateCustomAttributes($customValues, $attributes)
    {
        $errors = array();
        foreach ($attributes as $field => $data) {
            if (empty($customValues[$field])) {
                if (!empty($data['frontend']) && is_array(
                    $data['frontend']
                ) && !empty($data['frontend']['is_required'])
                ) {
                    $errors[] = __('Please enter the "%1".', $data['label']);
                }
            } else {
                if ($data['type'] == 'select' && is_array($data['options'])) {
                    $found = false;
                    foreach ($data['options'] as $option) {
                        if ($customValues[$field] == $option['code']) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $errors[] = __('Please enter the correct "%1".', $data['label']);
                    }
                }
            }
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Return list of gift registries
     *
     * @return \Magento\GiftRegistry\Model\Resource\GiftRegistry\Collection
     */
    public function getCurrentCustomerEntityOptions()
    {
        $result = array();
        $entityCollection = $this->entityFactory->create()->getCollection()->filterByCustomerId(
            $this->customerSession->getCustomerId()
        )->filterByIsActive(
            1
        );

        if (count($entityCollection)) {
            foreach ($entityCollection as $entity) {
                $result[] = new \Magento\Object(
                    array('value' => $entity->getId(), 'title' => $this->_escaper->escapeHtml($entity->getTitle()))
                );
            }
        }
        return $result;
    }

    /**
     * Format custom dates to internal format
     *
     * @param array|string $data
     * @param array $fieldDateFormats
     *
     * @return array|string
     */
    public function filterDatesByFormat($data, $fieldDateFormats)
    {
        if (!is_array($data)) {
            return $data;
        }
        foreach ($data as $id => $field) {
            if (!empty($data[$id])) {
                if (!is_array($field)) {
                    if (isset($fieldDateFormats[$id])) {
                        $data[$id] = $this->_filterDate($data[$id], $fieldDateFormats[$id]);
                    }
                } else {
                    foreach ($field as $id2 => $field2) {
                        if (!empty($data[$id][$id2]) && !is_array($field2) && isset($fieldDateFormats[$id2])) {
                            $data[$id][$id2] = $this->_filterDate($data[$id][$id2], $fieldDateFormats[$id2]);
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Convert date in from <$formatIn> to internal format
     *
     * @param string $value
     * @param string|bool $formatIn  -  FORMAT_TYPE_FULL, FORMAT_TYPE_LONG, FORMAT_TYPE_MEDIUM, FORMAT_TYPE_SHORT
     * @return string
     */
    public function _filterDate($value, $formatIn = false)
    {
        if ($formatIn === false) {
            return $value;
        } else {
            $formatIn = $this->_localeDate->getDateFormat($formatIn);
        }
        $filterInput = new \Zend_Filter_LocalizedToNormalized(
            array('date_format' => $formatIn, 'locale' => $this->_localeResolver->getLocaleCode())
        );
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(
            array('date_format' => \Magento\Stdlib\DateTime::DATE_INTERNAL_FORMAT)
        );

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);

        return $value;
    }

    /**
     * Return frontend registry link
     *
     * @param \Magento\GiftRegistry\Model\Entity $entity
     * @return string
     */
    public function getRegistryLink($entity)
    {
        return $this->urlFactory->create()->setScope(
            $entity->getStoreId()
        )->getUrl(
            'giftregistry/view/index',
            array('id' => $entity->getUrlKey())
        );
    }

    /**
     * Check if product can be added to gift registry
     *
     * @param Item|Product $item
     * @return bool
     */
    public function canAddToGiftRegistry($item)
    {
        if ($item->getIsVirtual()) {
            return false;
        }

        if ($item instanceof Item) {
            $productType = $item->getProductType();
        } else {
            $productType = $item->getTypeId();
        }

        if ($productType == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD) {
            if ($item instanceof Item) {
                $product = $this->productFactory->create()->load($item->getProductId());
            } else {
                $product = $item;
            }
            return $product->getTypeInstance()->isTypePhysical($product);
        }
        return true;
    }
}
