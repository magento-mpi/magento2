<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Category\Attribute\Source;

/**
 * Catalog category landing page attribute source
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Page extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Block collection factory
     *
     * @var \Magento\Cms\Model\Resource\Block\CollectionFactory
     */
    protected $_blockCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Cms\Model\Resource\Block\CollectionFactory
     * $blockCollectionFactory
     */
    public function __construct(\Magento\Cms\Model\Resource\Block\CollectionFactory $blockCollectionFactory)
    {
        $this->_blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * Return all block options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_blockCollectionFactory->create()->load()->toOptionArray();
            array_unshift($this->_options, array('value' => '', 'label' => __('Please select a static block.')));
        }
        return $this->_options;
    }
}
