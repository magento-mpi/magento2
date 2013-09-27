<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Block_Adminhtml_Renderer_Amount
 extends Magento_Backend_Block_Widget
 implements Magento_Data_Form_Element_Renderer_Interface
{
    protected $_element = null;
    protected $_websites = null;

    protected $_template = 'renderer/amount.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Directory helper
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryHelper;

    /**
     * @param Magento_Directory_Helper_Data $directoryHelper
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Directory_Helper_Data $directoryHelper,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_directoryHelper = $directoryHelper;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     *  Render Amounts Element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $isAddButtonDisabled = ($element->getData('readonly_disabled') === true) ? true : false;
        $this->addChild('add_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Add Amount'),
            'onclick'   => "giftcardAmountsControl.addItem('" . $this->getElement()->getHtmlId() . "')",
            'class'     => 'action-add',
            'disabled'  => $isAddButtonDisabled
        ));

        return $this->toHtml();
    }

    public function setElement(Magento_Data_Form_Element_Abstract $element)
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
        return !$this->_storeManager->hasSingleStore();
    }

    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name'      => __('All Websites'),
            'currency'  => $this->_directoryHelper->getBaseCurrencyCode()
        );

        if (!$this->_storeManager->hasSingleStore() && !$this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            $storeId = $this->getProduct()->getStoreId();
            if ($storeId) {
                $website = $this->_storeManager->getStore($storeId)->getWebsite();
                $websites[$website->getId()] = array(
                    'name'      => $website->getName(),
                    'currency'  => $website->getConfig(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                );
            } else {
                foreach ($this->_storeManager->getWebsites() as $website) {
                    if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                        continue;
                    }
                    $websites[$website->getId()] = array(
                        'name'      => $website->getName(),
                        'currency'  => $website->getConfig(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
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
