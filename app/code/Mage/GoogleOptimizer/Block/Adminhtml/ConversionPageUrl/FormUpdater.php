<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdater
{
    /**
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     */
    public function __construct(
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_GoogleOptimizer_Helper_Data $helper
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
    }

    /**
     * Add field conversion page url to form
     *
     * @param int $storeId
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    public function update($storeId, Varien_Data_Form_Element_Fieldset $fieldset)
    {
        if ($storeId == '0' && !$this->_storeManager->hasSingleStore()) {
            $fieldset->addField('conversion_page_url', 'note',
                array(
                    'name'  => 'conversion_page_url',
                    'label' => $this->_helper->__('Conversion Page URL'),
                    'text' => $this->_helper->__('Please select store view to see the URL.')
                )
            );
        } else {
            $fieldset->addField('conversion_page_url', 'text',
                array(
                    'name'  => 'conversion_page_url',
                    'label' => $this->_helper->__('Conversion Page URL'),
                    'class' => 'input-text',
                    'readonly' => 'readonly',
                    'required' => false,
                    'note' => $this->_helper->__('Please copy and paste this value to experiment edit form.')
                )
            );
        }
    }

}
