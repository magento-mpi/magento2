<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Adminhtml_Block_Tax_Rate_ImportExport extends Magento_Adminhtml_Block_Widget
{
    protected $_template = 'tax/importExport.phtml';

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        parent::__construct($context, $coreStoreConfig, $data);
        $this->setUseContainer(true);
    }


}
