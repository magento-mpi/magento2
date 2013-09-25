<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rma Item Form Renderer Block for select
 */
class Magento_Rma_Block_Form_Renderer_Select extends Magento_CustomAttribute_Block_Form_Renderer_Select
{
    /**
     * @var Magento_Rma_Model_ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var Magento_Rma_Model_Item_Form
     */
    protected $_itemFormFactory;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Rma_Model_ItemFactory $itemFactory
     * @param Magento_Rma_Model_Item_FormFactory $itemFormFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Rma_Model_ItemFactory $itemFactory,
        Magento_Rma_Model_Item_FormFactory $itemFormFactory,
        array $data = array()
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_itemFormFactory = $itemFormFactory;
        parent::__construct($locale, $coreData, $context, $data);
    }

    /**
     * Prepare rma item attribute
     *
     * @param string $code
     * @return bool|Magento_Rma_Model_Item_Attribute
     */
    public function getAttribute($code)
    {
        /* @var $itemModel  */
        $itemModel = $this->_itemFactory->create();
        /* @var $itemForm Magento_Rma_Model_Item_Form */
        $itemForm = $this->_itemFormFactory->create();
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        $attribute = $itemForm->getAttribute($code);
        if ($attribute->getIsVisible()) {
            return $attribute;
        }
        return false;
    }
}

