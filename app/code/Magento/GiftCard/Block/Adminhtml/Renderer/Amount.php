<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Block\Adminhtml\Renderer;

class Amount
 extends \Magento\Adminhtml\Block\Widget
 implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    protected $_element = null;
    protected $_websites = null;

    protected $_template = 'renderer/amount.phtml';

    public function getProduct()
    {
        return \Mage::registry('product');
    }

    /**
     *  Render Amounts Element
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        $isAddButtonDisabled = ($element->getData('readonly_disabled') === true) ? true : false;
        $this->addChild('add_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Add Amount'),
            'onclick'   => "giftcardAmountsControl.addItem('" . $this->getElement()->getHtmlId() . "')",
            'class'     => 'action-add',
            'disabled'  => $isAddButtonDisabled
        ));

        return $this->toHtml();
    }

    public function setElement(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    public function isMultiWebsites()
    {
        return !\Mage::app()->hasSingleStore();
    }

    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name'      => __('All Websites'),
            'currency'  => \Mage::app()->getBaseCurrencyCode()
        );

        if (!\Mage::app()->hasSingleStore() && !$this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            if ($storeId = $this->getProduct()->getStoreId()) {
                $website = \Mage::app()->getStore($storeId)->getWebsite();
                $websites[$website->getId()] = array(
                    'name'      => $website->getName(),
                    'currency'  => $website->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
                );
            } else {
                foreach (\Mage::app()->getWebsites() as $website) {
                    if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                        continue;
                    }
                    $websites[$website->getId()] = array(
                        'name'      => $website->getName(),
                        'currency'  => $website->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE),
                    );
                }
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

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

    protected function _sortValues($a, $b)
    {
        if ($a['website_id']!=$b['website_id']) {
            return $a['website_id']<$b['website_id'] ? -1 : 1;
        }
        return 0;
    }
}
