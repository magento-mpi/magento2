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
    extends \Magento\Catalog\Block\Adminhtml\Category\AbstractCategory
{
    /**
     * Factory for event collections
     *
     * @var \Magento\CatalogEvent\Model\Resource\Event\CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * @var \Magento\CatalogEvent\Helper\Data
     */
    protected $_catalogeventHelper;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Resource\Category\Tree $categoryTree
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\CatalogEvent\Model\Resource\Event\CollectionFactory $eventCollectionFactory
     * @param \Magento\CatalogEvent\Helper\Data $catalogeventHelper
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Resource\Category\Tree $categoryTree,
        \Magento\Core\Model\Registry $registry,
        \Magento\CatalogEvent\Model\Resource\Event\CollectionFactory $eventCollectionFactory,
        \Magento\CatalogEvent\Helper\Data $catalogeventHelper,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = array()
    ) {
        $this->_backendHelper = $backendHelper;
        $this->_catalogeventHelper = $catalogeventHelper;
        parent::__construct($context, $categoryTree, $registry, $data);

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
        if ($this->_catalogeventHelper->isEnabled()
            && $this->_authorization->isAllowed('Magento_CatalogEvent::events')
            && $this->getCategoryId() && $this->getCategory()->getLevel() > 1) {
            if ($this->getEvent() && $this->getEvent()->getId()) {
                $url = $this->_backendHelper->getUrl('adminhtml/catalog_event/edit', array(
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
                $url = $this->_backendHelper->getUrl('adminhtml/catalog_event/new', array(
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
