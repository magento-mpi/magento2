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
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(Mage_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }


}
