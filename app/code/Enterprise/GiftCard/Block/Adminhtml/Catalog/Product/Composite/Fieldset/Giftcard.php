<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Giftcard
    extends Enterprise_GiftCard_Block_Catalog_Product_View_Type_Giftcard
{
    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Checks whether block is last fieldset in popup
     *
     * @return bool
     */
    public function getIsLastFieldset()
    {
        if ($this->hasData('is_last_fieldset')) {
            return $this->getData('is_last_fieldset');
        } else {
            return !$this->getProduct()->getOptions();
        }
    }

    /**
     * @return Mage_Core_Model_StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
