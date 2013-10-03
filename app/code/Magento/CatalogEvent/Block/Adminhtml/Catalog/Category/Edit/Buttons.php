<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events edit form select categories
 */
namespace Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit;

class Buttons
    extends \Magento\Adminhtml\Block\Catalog\Category\AbstractCategory
{
    /**
     * Factory for event collections
     *
     * @var \Magento\CatalogEvent\Model\Resource\Event\CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * @param \Magento\Catalog\Model\Resource\Category\Tree $categoryTree
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\CatalogEvent\Model\Resource\Event\CollectionFactory $eventCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Category\Tree $categoryTree,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\CatalogEvent\Model\Resource\Event\CollectionFactory $eventCollectionFactory,
        array $data = array()
    ) {
        parent::__construct($categoryTree, $coreData, $context, $registry, $data);

        $this->_eventCollectionFactory = $eventCollectionFactory;
    }

    /**
     * Retrieve category event
     *
     * @return \Magento\CatalogEvent\Model\Event
     */
    public function getEvent()
    {
        if (!$this->hasData('event')) {
            /** @var \Magento\CatalogEvent\Model\Resource\Event\Collection $collection */
            $collection = $this->_eventCollectionFactory->create()
                ->addFieldToFilter('category_id', $this->getCategoryId());

            $event = $collection->getFirstItem();
            $this->setData('event', $event);
        }

        return $this->getData('event');
    }

    /**
     * Add buttons on category edit page
     *
     * @return \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\Buttons
     */
    public function addButtons()
    {
        if ($this->helper('Magento\CatalogEvent\Helper\Data')->isEnabled()
            && $this->_authorization->isAllowed('Magento_CatalogEvent::events')
            && $this->getCategoryId() && $this->getCategory()->getLevel() > 1) {
            if ($this->getEvent() && $this->getEvent()->getId()) {
                $url = $this->helper('Magento\Adminhtml\Helper\Data')->getUrl('*/catalog_event/edit', array(
                            'id' => $this->getEvent()->getId(),
                            'category' => 1
                ));
                $this->getParentBlock()->getChildBlock('form')
                    ->addAdditionalButton('edit_event', array(
                        'label' => __('Edit Event...'),
                        'class' => 'save',
                        'onclick'   => 'setLocation(\''. $url .'\')'
                    ));
            } else {
                $url = $this->helper('Magento\Adminhtml\Helper\Data')->getUrl('*/catalog_event/new', array(
                        'category_id' => $this->getCategoryId(),
                        'category' => 1
                ));
                $this->getParentBlock()->getChildBlock('form')
                    ->addAdditionalButton('add_event', array(
                        'label' => __('Add Event...'),
                        'class' => 'add',
                        'onclick' => 'setLocation(\''. $url .'\')'
                    ));
            }
        }
        return $this;
    }
}
