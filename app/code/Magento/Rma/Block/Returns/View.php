<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Returns;

use Magento\Rma\Model\Item;
use Magento\Rma\Model\Rma;

/**
 * Class View
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Rma\Block\Form
{
    /**
     * Values for each visible attribute
     *
     * @var array
     */
    protected $_realValueAttributes = array();

    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerView = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Rma item collection
     *
     * @var \Magento\Rma\Model\Resource\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * Rma status history collection
     *
     * @var \Magento\Rma\Model\Resource\Rma\Status\History\CollectionFactory
     */
    protected $_historiesFactory;

    /**
     * Rma item factory
     *
     * @var \Magento\Rma\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * Eav model form factory
     *
     * @var \Magento\Rma\Model\Item\FormFactory
     */
    protected $_itemFormFactory;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Customer\Service\V1\Data\Customer
     */
    protected $customerData;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService
     */
    protected $currentCustomer;
    
    /**
     * Eav configuration model
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Rma\Model\Resource\Item\CollectionFactory $itemsFactory
     * @param \Magento\Rma\Model\Resource\Rma\Status\History\CollectionFactory $historiesFactory
     * @param \Magento\Rma\Model\ItemFactory $itemFactory
     * @param Item\FormFactory $itemFormFactory
     * @param \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Helper\View $customerView
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Rma\Model\Resource\Item\CollectionFactory $itemsFactory,
        \Magento\Rma\Model\Resource\Rma\Status\History\CollectionFactory $historiesFactory,
        \Magento\Rma\Model\ItemFactory $itemFactory,
        \Magento\Rma\Model\Item\FormFactory $itemFormFactory,
        \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Helper\View $customerView,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_itemsFactory = $itemsFactory;
        $this->_historiesFactory = $historiesFactory;
        $this->_itemFactory = $itemFactory;
        $this->_itemFormFactory = $itemFormFactory;
        $this->currentCustomer = $currentCustomer;
        $this->_customerAccountService = $customerAccountService;
        $this->_customerView = $customerView;
        $this->_rmaData = $rmaData;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $modelFactory, $formFactory, $eavConfig, $data);
    }

    /**
     * Initialize rma return
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        if (!$this->_coreRegistry->registry('current_rma')) {
            return;
        }
        $this->setTemplate('return/view.phtml');

        $this->setRma($this->_coreRegistry->registry('current_rma'));
        $this->setOrder($this->_coreRegistry->registry('current_order'));

        /** @var $collection \Magento\Rma\Model\Resource\Item\Collection */
        $collection = $this->_itemsFactory->create()->addAttributeToSelect(
            '*'
        )->addFilter(
            'rma_entity_id',
            $this->getRma()->getEntityId()
        );

        $this->setItems($collection);

        /** @var $comments \Magento\Rma\Model\Resource\Rma\Status\History\Collection */
        $comments = $this->_historiesFactory->create()->addFilter('rma_entity_id', $this->getRma()->getEntityId());
        $this->setComments($comments);
    }

    /**
     * Returns attributes that static
     *
     * @return string[]
     */
    public function getAttributeFilter()
    {
        $array = array();

        /** @var $collection \Magento\Rma\Model\Resource\Item\Collection */
        $collection = $this->_itemsFactory->create();
        $collection->addFilter('rma_entity_id', $this->getRma()->getEntityId());
        foreach ($collection as $item) {
            foreach (array_keys($item->getData()) as $attributeCode) {
                $array[] = $attributeCode;
            }
            break;
        }

        /* @var $itemModel Item */
        $itemModel = $this->_itemFactory->create();

        /* @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm = $this->_itemFormFactory->create();
        $itemForm->setFormCode('default')->setStore($this->getStore())->setEntity($itemModel);

        // add system required attributes
        foreach ($itemForm->getSystemAttributes() as $attribute) {
            /* @var $attribute \Magento\Rma\Model\Item\Attribute */
            if ($attribute->getIsVisible()) {
                $array[] = $attribute->getAttributeCode();
            }
        }

        return $array;
    }

    /**
     * Gets values for each visible attribute
     *
     * Parameter $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param string[] $excludeAttr
     * @return array
     */
    protected function _getAdditionalData(array $excludeAttr = array())
    {
        $data = array();

        $items = $this->getItems();

        $itemForm = false;

        foreach ($items as $item) {
            if (!$itemForm) {
                /* @var $itemForm \Magento\Rma\Model\Item\Form */
                $itemForm = $this->_itemFormFactory->create();
                $itemForm->setFormCode('default')->setStore($this->getStore())->setEntity($item);
            }
            foreach ($itemForm->getAttributes() as $attribute) {
                $code = $attribute->getAttributeCode();
                if ($attribute->getIsVisible() && !in_array($code, $excludeAttr)) {
                    $value = $attribute->getFrontend()->getValue($item);
                    $data[$item->getId()][$code] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'html' => ''
                    );
                    if ($attribute->getFrontendInput() == 'image') {
                        $data[$item->getId()][$code]['html'] = $this->setEntity($item)->getAttributeHtml($attribute);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Gets attribute value by rma item id and attribute code
     *
     * @param  int $itemId
     * @param  string $attributeCode
     * @return string
     */
    public function getAttributeValue($itemId, $attributeCode)
    {
        if (empty($this->_realValueAttributes)) {
            $this->_realValueAttributes = $this->_getAdditionalData();
        }

        if (!empty($this->_realValueAttributes[$itemId][$attributeCode]['html'])) {
            $html = $this->_realValueAttributes[$itemId][$attributeCode]['html'];
        } else {
            $html = $this->escapeHtml($this->_realValueAttributes[$itemId][$attributeCode]['value']);
        }
        return $html;
    }

    /**
     * Gets values for each visible attribute depending on item id
     *
     * @param null|int $itemId
     * @return array
     */
    public function getRealValueAttributes($itemId = null)
    {
        if (empty($this->_realValueAttributes)) {
            $this->_realValueAttributes = $this->_getAdditionalData();
        }
        if ($itemId && isset($this->_realValueAttributes[$itemId])) {
            return $this->_realValueAttributes[$itemId];
        } else {
            return $this->_realValueAttributes;
        }
    }

    /**
     * Gets attribute label by rma item id and attribute code
     *
     * @param  int $itemId
     * @param  string $attributeCode
     * @return string|false
     */
    public function getAttributeLabel($itemId, $attributeCode)
    {
        if (empty($this->_realValueAttributes)) {
            $this->_realValueAttributes = $this->_getAdditionalData();
        }

        if (isset($this->_realValueAttributes[$itemId][$attributeCode])) {
            return $this->_realValueAttributes[$itemId][$attributeCode]['label'];
        }

        return false;
    }

    /**
     * Gets item options
     *
     * @param  Item $item
     * @return array|bool
     */
    public function getItemOptions($item)
    {
        return $item->getOptions();
    }

    /**
     * Get sales order view url
     *
     * @param Rma $rma
     * @return string
     */
    public function getOrderUrl($rma)
    {
        return $this->getUrl('sales/order/view/', array('order_id' => $rma->getOrderId()));
    }

    /**
     * Get rma returns back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
            return $this->getUrl('rma/returns/history');
        } else {
            return $this->getUrl('rma/guest/returns');
        }
    }

    /**
     * Get return address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->_rmaData->getReturnAddress();
    }

    /**
     * Get add comment submit url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('entity_id' => (int)$this->getRequest()->getParam('entity_id')));
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
            return $this->_customerView->getCustomerName($this->getCustomerData());
        } else {
            $billingAddress = $this->_coreRegistry->registry('current_order')->getBillingAddress();

            $name = '';
            if ($this->_eavConfig->getAttribute('customer', 'prefix')->getIsVisible() && $billingAddress->getPrefix()
            ) {
                $name .= $billingAddress->getPrefix() . ' ';
            }
            $name .= $billingAddress->getFirstname();
            if ($this->_eavConfig->getAttribute(
                'customer',
                'middlename'
            )->getIsVisible() && $billingAddress->getMiddlename()
            ) {
                $name .= ' ' . $billingAddress->getMiddlename();
            }
            $name .= ' ' . $billingAddress->getLastname();
            if ($this->_eavConfig->getAttribute('customer', 'suffix')->getIsVisible() && $billingAddress->getSuffix()
            ) {
                $name .= ' ' . $billingAddress->getSuffix();
            }
            return $name;
        }
    }

    /**
     * @return \Magento\Customer\Service\V1\Data\Customer|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerData()
    {
        if (empty($this->customerData)) {
            $customerId = $this->currentCustomer->getCustomerId();
            $this->customerData = $this->_customerAccountService->getCustomer($customerId);
        }
        return $this->customerData;
    }

    /**
     * Get html data of tracking info block. Namely list of rows in table
     *
     * @return string
     */
    public function getTrackingInfo()
    {
        return $this->getBlockHtml('rma.returns.tracking');
    }

    /**
     * Get collection of tracking numbers of RMA
     *
     * @return \Magento\Rma\Model\Resource\Shipping\Collection
     */
    public function getTrackingNumbers()
    {
        return $this->getRma()->getTrackingNumbers();
    }

    /**
     * Get shipping label of RMA
     *
     * @return \Magento\Rma\Model\Shipping
     */
    public function getShippingLabel()
    {
        return $this->getRma()->getShippingLabel();
    }

    /**
     * Get shipping label of RMA
     *
     * @return \Magento\Rma\Model\Shipping
     */
    public function canShowButtons()
    {
        return (bool)($this->getShippingLabel()->getId() &&
            !($this->getRma()->getStatus() == \Magento\Rma\Model\Rma\Source\Status::STATE_CLOSED ||
            $this->getRma()->getStatus() == \Magento\Rma\Model\Rma\Source\Status::STATE_PROCESSED_CLOSED));
    }

    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['id'] = $this->getRma()->getId();
        $url = $this->getUrl('*/rma/printLabel', $data);
        return $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Link'
        )->setData(
            array('label' => __('Print Shipping Label'), 'onclick' => 'setLocation(\'' . $url . '\')')
        )->setAnchorText(
            __('Print Shipping Label')
        )->toHtml();
    }

    /**
     * Show packages button html
     *
     * @return string
     */
    public function getShowPackagesButton()
    {
        return $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Link'
        )->setData(
            array(
                'href' => "javascript:void(0)",
                'title' => __('Show Packages'),
                'onclick' => "popWin(
                        '" .
                    $this->_rmaData->getPackagePopupUrlByRmaModel(
                        $this->getRma()
                    ) .
                "',
                        'package',
                        'width=800,height=600,top=0,left=0,resizable=yes,scrollbars=yes'); return false;"
            )
        )->setAnchorText(
            __('Show Packages')
        )->toHtml();
    }

    /**
     * Show print shipping label html
     *
     * @return string
     */
    public function getPrintShippingLabelButton()
    {
        return $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Link'
        )->setData(
            array(
                'href' => $this->_rmaData->getPackagePopupUrlByRmaModel($this->getRma(), 'printlabel'),
                'title' => __('Print Shipping Label')
            )
        )->setAnchorText(
            __('Print Shipping Label')
        )->toHtml();
    }

    /**
     * Get list of shipping carriers for select
     *
     * @return array
     */
    public function getCarriers()
    {
        return $this->_rmaData->getShippingCarriers($this->getRma()->getStoreId());
    }

    /**
     * Get url for add label action
     *
     * @return string
     */
    public function getAddLabelUrl()
    {
        return $this->getUrl('*/*/addLabel/', array('entity_id' => $this->getRma()->getEntityId()));
    }

    /**
     * Get whether rma and allowed
     *
     * @return bool
     */
    public function isPrintShippingLabelAllowed()
    {
        return $this->getRma()->isAvailableForPrintLabel();
    }
}
