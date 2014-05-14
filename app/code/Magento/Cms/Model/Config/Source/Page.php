<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Config\Source;

class Page implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\Cms\Model\Resource\Page\CollectionFactory
     */
    protected $_pageCollectionFactory;

    /**
     * @param \Magento\Cms\Model\Resource\Page\CollectionFactory $pageCollectionFactory
     */
    public function __construct(\Magento\Cms\Model\Resource\Page\CollectionFactory $pageCollectionFactory)
    {
        $this->_pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_pageCollectionFactory->create()->load()->toOptionIdArray();
        }
        return $this->_options;
    }
}
