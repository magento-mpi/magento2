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

use Magento\Sales\Model\Order\Item;
use Magento\Rma\Model\Item\Attribute;

class Create extends \Magento\Rma\Block\Form
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Rma\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Rma\Model\Item\FormFactory
     */
    protected $_itemFormFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Rma\Model\ItemFactory $itemFactory
     * @param \Magento\Rma\Model\Item\FormFactory $itemFormFactory
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Rma\Model\ItemFactory $itemFactory,
        \Magento\Rma\Model\Item\FormFactory $itemFormFactory,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rmaData = $rmaData;
        $this->_itemFactory = $itemFactory;
        $this->_itemFormFactory = $itemFormFactory;
        parent::__construct($context, $modelFactory, $formFactory, $eavConfig, $data);
        $this->_isScopePrivate = true;
    }

    public function _construct()
    {
        $order = $this->_coreRegistry->registry('current_order');
        if (!$order) {
            return;
        }
        $this->setOrder($order);

        $items = $this->_rmaData->getOrderItems($order);
        $this->setItems($items);

        $formData = $this->_session->getRmaFormData(true);
        if (!empty($formData)) {
            $data = new \Magento\Object();
            $data->addData($formData);
            $this->setFormData($data);
        }
        $errorKeys = $this->_session->getRmaErrorKeys(true);
        if (!empty($errorKeys)) {
            $data = new \Magento\Object();
            $data->addData($errorKeys);
            $this->setErrorKeys($data);
        }
    }

    /**
     * Retrieves item qty available for return
     *
     * @param  Item $item
     * @return int
     */
    public function getAvailableQty($item)
    {
        $return = $item->getAvailableQty();
        if (!$item->getIsQtyDecimal()) {
            $return = intval($return);
        }
        return $return;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->_urlBuilder->getUrl('sales/order/history');
    }

    /**
     * Prepare rma item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $itemModel \Magento\Rma\Model\Item */
        $itemModel = $this->_itemFactory->create();

        /* @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm = $this->_itemFormFactory->create();
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        // prepare item attributes to show
        $attributes = array();

        // add system required attributes
        foreach ($itemForm->getSystemAttributes() as $attribute) {
            /* @var $attribute Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        // add user defined attributes
        foreach ($itemForm->getUserAttributes() as $attribute) {
            /* @var $attribute Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        uasort($attributes, array($this, '_compareSortOrder'));

        return $attributes;
    }

    /**
     * Retrieves Contact Email Address on error
     *
     * @return string
     */
    public function getContactEmail()
    {
        $data   = $this->getFormData();
        $email  = '';

        if ($data) {
            $email = $this->escapeHtml($data->getCustomerCustomEmail());
        }
        return $email;
    }

    /**
     * Compares sort order of attributes, returns -1, 0 or 1 if $a sort
     * order is less, equal or greater than $b sort order respectively.
     *
     * @param Attribute $a
     * @param Attribute $b
     *
     * @return int
     */
    protected function _compareSortOrder(Attribute $a, Attribute $b)
    {
        $diff = $a->getSortOrder() - $b->getSortOrder();
        return $diff ? ($diff > 0 ? 1 : -1) : 0;
    }
}
