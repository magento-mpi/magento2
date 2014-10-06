<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TaxImportExport\Block\Adminhtml\Rate;

class ImportExport extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'importExport.phtml';

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
