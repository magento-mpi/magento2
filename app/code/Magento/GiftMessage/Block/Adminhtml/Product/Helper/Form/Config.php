<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form;

/**
 * Adminhtml additional helper block for product configuration
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Config
    extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Config
{
    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        $data = array()
    ) {
        $this->_storeConfig = $coreStoreConfig;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Get config value data
     *
     * @return string|null
     */
    protected function _getValueFromConfig()
    {
        return $this->_storeConfig->getValue(
            \Magento\GiftMessage\Helper\Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS
        , \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }
}
