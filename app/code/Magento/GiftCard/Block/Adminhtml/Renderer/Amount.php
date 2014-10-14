<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Amount extends Widget implements RendererInterface
{
    /**
     * @var AbstractElement
     */
    protected $_element = null;

    /**
     * @var array
     */
    protected $_websites = null;

    /**
     * @var string
     */
    protected $_template = 'renderer/amount.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Directory helper
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_directoryHelper = $directoryHelper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     *  Render Amounts Element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        $isAddButtonDisabled = $element->getData('readonly_disabled') === true ? true : false;
        $this->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add Amount'),
                'onclick' => "giftcardAmountsControl.addItem('" . $this->getElement()->getHtmlId() . "')",
                'class' => 'action-add',
                'disabled' => $isAddButtonDisabled
            )
        );

        return $this->toHtml();
    }

    /**
     * @param AbstractElement $element
     * @return $this
     */
    public function setElement(AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * @return int
     */
    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    /**
     * @return bool
     */
    public function isMultiWebsites()
    {
        return !$this->_storeManager->hasSingleStore();
    }

    /**
     * @return array
     */
    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name' => __('All Websites'),
            'currency' => $this->_directoryHelper->getBaseCurrencyCode()
        );

        if (!$this->_storeManager->hasSingleStore() && !$this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            $storeId = $this->getProduct()->getStoreId();
            if ($storeId) {
                $website = $this->_storeManager->getStore($storeId)->getWebsite();
                $websites[$website->getId()] = array(
                    'name' => $website->getName(),
                    'currency' => $website->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE)
                );
            } else {
                foreach ($this->_storeManager->getWebsites() as $website) {
                    if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                        continue;
                    }
                    $websites[$website->getId()] = array(
                        'name' => $website->getName(),
                        'currency' => $website->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE)
                    );
                }
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }

    /**
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $data = $this->getElement()->getValue();

        if (is_array($data) && count($data)) {
            usort($data, array($this, '_sortValues'));
            $values = $data;
        }
        return $values;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortValues($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }

        return $a['value'] < $b['value'] ? -1 : 1;
    }
}
