<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events Adminhtml controller
 */
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\CatalogEvent\Model\EventFactory;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;

class Event extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Event model factory
     *
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * @var DateTime
     */
    protected $_dateTimeFilter;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param EventFactory $eventFactory
     * @param DateTime $dateTimeFilter
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        EventFactory $eventFactory,
        DateTime $dateTimeFilter,
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_eventFactory = $eventFactory;
        $this->_dateTimeFilter = $dateTimeFilter;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\CatalogEvent\Helper\Data')->isEnabled()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
                return parent::dispatch($request);
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Init action breadcrumbs and active menu
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(__('Events'), __('Events'));
        $this->_setActiveMenu('Magento_CatalogEvent::catalog_magento_catalogevent_events');
        return $this;
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogEvent::events');
    }
}
