<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Tax\Block\Adminhtml\Rate;

class ImportExport extends \Magento\Adminhtml\Block\Widget
{
    protected $_template = 'importExport.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->setUseContainer(true);
    }
}
