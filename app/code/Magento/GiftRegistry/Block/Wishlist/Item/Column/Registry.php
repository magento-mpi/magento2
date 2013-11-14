<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Wishlist\Item\Column;

/**
 * Wishlist item "Add to gift registry" column block
 */
class Registry extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData;

    /**
     * Filter manager
     *
     * @var \Magento\Filter\FilterManager
     */
    protected $filter;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Filter\FilterManager $filter
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Math\Random $mathRandom,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Filter\FilterManager $filter,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->filter = $filter;
        parent::__construct(
            $storeManager,
            $catalogConfig,
            $coreRegistry,
            $taxData,
            $catalogData,
            $coreData,
            $context,
            $mathRandom,
            $wishlistData,
            $customerSession,
            $productFactory,
            $data
        );
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filter->truncate($value, array(
            'length' => $length,
            'etc' => $etc,
            'remainder' => $remainder,
            'breakWords' => $breakWords
        ));
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_giftRegistryData->isEnabled() && count($this->getGiftRegistryList());
    }

    /**
     * Return list of current customer gift registries
     *
     * @return \Magento\GiftRegistry\Model\Resource\GiftRegistry\Collection
     */
    public function getGiftRegistryList()
    {
        return $this->_giftRegistryData->getCurrentCustomerEntityOptions();
    }

    /**
     * Check if wishlist item can be added to gift registry
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return bool
     */
    public function checkProductType($item)
    {
        return $this->_giftRegistryData->canAddToGiftRegistry($item);
    }

    /**
     * Retrieve column related javascript code
     *
     * @return string
     */
    public function getJs()
    {
        $addUrl = $this->getUrl('giftregistry/index/wishlist');
        return "
        function addProductToGiftregistry(itemId, giftregistryId) {
            var form = new Element('form', {method: 'post', action: '" . $addUrl . "'});
            form.insert(new Element('input', {type: 'hidden', name: 'item', value: itemId}));
            form.insert(new Element('input', {type: 'hidden', name: 'entity', value: giftregistryId}));
            $(document.body).insert(form);
            $(form).submit();
            return false;
        }
        ";
    }
}
