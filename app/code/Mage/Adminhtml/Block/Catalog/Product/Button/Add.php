<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Add new product" button block for catalog products grid
 */
class Mage_Adminhtml_Block_Catalog_Product_Button_Add extends Mage_Backend_Block_Widget_Button_Split
{
    /**
     * @var Mage_Catalog_Model_Product_Limitation
     */
    protected $_limitation;

    /**
     * Constructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Catalog_Model_Product_Limitation $limitation
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Catalog_Model_Product_Limitation $limitation,
        $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_limitation = $limitation;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSplit()
    {
        return !$this->_limitation->isCreateRestricted();
    }

    /**
     * Return, whether the button must be disabled
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisabled()
    {
        if ($this->hasData('disabled')) {
            return $this->_getData('disabled');
        }
        return $this->_limitation->isCreateRestricted();
    }
}
