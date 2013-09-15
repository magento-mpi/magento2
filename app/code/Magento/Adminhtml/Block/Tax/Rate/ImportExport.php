<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Adminhtml\Block\Tax\Rate;

class ImportExport extends \Magento\Adminhtml\Block\Widget
{
    protected $_template = 'tax/importExport.phtml';

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->setUseContainer(true);
    }
}
