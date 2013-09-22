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

class View extends \Magento\Rma\Block\Form
{
    /**
     * Values for each visible attribute
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
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Factory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_customerData = $customerData;
        $this->_rmaData = $rmaData;
        $this->_coreRegistry = $registry;
        parent::__construct($modelFactory, $formFactory, $eavConfig, $coreData, $context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        if (!$this->_coreRegistry->registry('current_rma')) {
            return;
        }
        $this->setTemplate('return/view.phtml');

        $this->setRma($this->_coreRegistry->registry('current_rma'));
        $this->setOrder($this->_coreRegistry->registry('current_order'));

        /** @var $collection \Magento\Rma\Model\Resource\Item */
        $collection = \Mage::getResourceModel('Magento\Rma\Model\Resource\Item\Collection')
            ->addAttributeToSelect('*')
            ->addFilter('rma_entity_id', $this->getRma()->getEntityId())
        ;

        $this->setItems($collection);

        /** @var $comments \Magento\Rma\Model\Resource\Rma\Status\History\Collection */
        $comments = \Mage::getResourceModel('Magento\Rma\Model\Resource\Rma\Status\History\Collection')
            ->addFilter('rma_entity_id', $this->getRma()->getEntityId())
        ;
        $this->setComments($comments);
    }

    /**
     * Returns attributes that static
     *
     * @return array
     */
    public function getAttributeFilter()
    {
        $array = array();

        /** @var $collection \Magento\Rma\Model\Resource\Item */
        $collection = \Mage::getResourceModel('Magento\Rma\Model\Resource\Item\Collection')
            ->addFilter('rma_entity_id', $this->getRma()->getEntityId())
        ;
        foreach ($collection as $item) {
            foreach ($item->getData() as $attributeCode=>$value) {
                $array[] = $attributeCode;
            }
            break;
        }

        /* @var $itemModel \Magento\Rma\Model\Item */
        $itemModel = \Mage::getModel('Magento\Rma\Model\Item');

        /* @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm   = \Mage::getModel('Magento\Rma\Model\Item\Form');
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

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
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     */
    protected function _getAdditionalData(array $excludeAttr = array())
    {
        $data       = array();

        $items      = $this->getItems();

        $itemForm   = false;

        foreach ($items as $item) {
            if (!$itemForm) {
                /* @var $itemForm \Magento\Rma\Model\Item\Form */
                $itemForm   = \Mage::getModel('Magento\Rma\Model\Item\Form');
                $itemForm->setFormCode('default')
                    ->setStore($this->getStore())
                    ->setEntity($item);
            }
            foreach ($itemForm->getAttributes() as $attribute) {
                $code = $attribute->getAttributeCode();
                if ($attribute->getIsVisible() && !in_array($code, $excludeAttr)) {
                    $value = $attribute->getFrontend()->getValue($item);
                    $data[$item->getId()][$code] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'html'  => ''
                    );
                    if ($attribute->getFrontendInput() == 'image') {
                        $data[$item->getId()][$code]['html'] = $this->setEntity($item)
                            ->getAttributeHtml($attribute);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Gets attribute value by rma item id and attribute code
     *
     * @param  $itemId
     * @param  $attributeCode
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
    public function getRealValueAttributes($itemId = null) {
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
     * @param  $itemId
     * @param  $attributeCode
     * @return string | bool
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
     * @param  $item \Magento\Rma\Model\Item
     * @return array | bool
     */
    public function getItemOptions($item)
    {
        return $item->getOptions();
    }

    public function getOrderUrl($rma)
    {
        return $this->getUrl('sales/order/view/', array('order_id' => $rma->getOrderId()));
    }

    public function getBackUrl()
    {
        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return $this->getUrl('rma/returns/history');
        } else {
            return $this->getUrl('rma/guest/returns');
        }
    }

    public function getAddress()
    {
        return  $this->_rmaData->getReturnAddress();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('entity_id' => (int)$this->getRequest()->getParam('entity_id')));
    }

    public function getCustomerName()
    {
        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return $this->_customerData->getCustomerName();
        } else {
            $billingAddress = $this->_coreRegistry->registry('current_order')->getBillingAddress();

            $name = '';
            $config = \Mage::getSingleton('Magento\Eav\Model\Config');
            if ($config->getAttribute('customer', 'prefix')->getIsVisible() && $billingAddress->getPrefix()) {
                $name .= $billingAddress->getPrefix() . ' ';
            }
            $name .= $billingAddress->getFirstname();
            if ($config->getAttribute('customer', 'middlename')->getIsVisible() && $billingAddress->getMiddlename()) {
                $name .= ' ' . $billingAddress->getMiddlename();
            }
            $name .=  ' ' . $billingAddress->getLastname();
            if ($config->getAttribute('customer', 'suffix')->getIsVisible() && $billingAddress->getSuffix()) {
                $name .= ' ' . $billingAddress->getSuffix();
            }
            return $name;
        }
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
        return (bool)(
            $this->getShippingLabel()->getId()
            && (!($this->getRma()->getStatus() == \Magento\Rma\Model\Rma\Source\Status::STATE_CLOSED
                || $this->getRma()->getStatus() == \Magento\Rma\Model\Rma\Source\Status::STATE_PROCESSED_CLOSED))
        );
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
        return $this->getLayout()
            ->createBlock('Magento\Core\Block\Html\Link')
            ->setData(array(
                'label'   => __('Print Shipping Label'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->setAnchorText(__('Print Shipping Label'))
            ->toHtml();
    }

    /**
     * Show packages button html
     *
     * @return string
     */
    public function getShowPackagesButton()
    {
        return $this->getLayout()
            ->createBlock('Magento\Core\Block\Html\Link')
            ->setData(array(
                'href'      => "javascript:void(0)",
                'title'     => __('Show Packages'),
                'onclick'   => "popWin(
                        '".$this->helper('Magento\Rma\Helper\Data')->getPackagePopupUrlByRmaModel($this->getRma())."',
                        'package',
                        'width=800,height=600,top=0,left=0,resizable=yes,scrollbars=yes'); return false;"
            ))
            ->setAnchorText(__('Show Packages'))
            ->toHtml();
    }

    /**
     * Show print shipping label html
     *
     * @return string
     */
    public function getPrintShippingLabelButton()
    {
        return $this->getLayout()
            ->createBlock('Magento\Core\Block\Html\Link')
            ->setData(array(
                'href'      => $this->helper('Magento\Rma\Helper\Data')->getPackagePopupUrlByRmaModel(
                    $this->getRma(),
                    'printlabel'
                ),
                'title'     => __('Print Shipping Label'),
            ))
            ->setAnchorText(__('Print Shipping Label'))
            ->toHtml();
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
