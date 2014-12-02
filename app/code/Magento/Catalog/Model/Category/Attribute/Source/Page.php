<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category\Attribute\Source;

use \Magento\Cms\Model\Resource\Block\Grid\CollectionFactory;

/**
 * Catalog category landing page attribute source
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Page extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Block collection factory
     *
     * @var CollectionFactory
     */
    protected $_blockCollectionFactory;

    /**
     * Construct
     *
     * @param CollectionFactory $blockCollectionFactory
     */
    public function __construct(CollectionFactory $blockCollectionFactory)
    {
        $this->_blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * {@inheritdoc}
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
