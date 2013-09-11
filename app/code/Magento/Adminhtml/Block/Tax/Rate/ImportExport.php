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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }


}
