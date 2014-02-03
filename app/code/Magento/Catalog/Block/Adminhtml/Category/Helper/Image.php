<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category form image field helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Category\Helper;

class Image extends \Magento\Data\Form\Element\Image
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        \Magento\UrlInterface $urlBuilder,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $urlBuilder, $data);
    }

    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\UrlInterface::URL_TYPE_MEDIA) . 'catalog/category/' . $this->getValue();
        }
        return $url;
    }
}
