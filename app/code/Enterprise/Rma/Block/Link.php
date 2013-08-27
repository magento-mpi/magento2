<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Return Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Link extends Magento_Core_Block_Template
{
    /**
     * Rma data
     *
     * @var Enterprise_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * @param Enterprise_Rma_Helper_Data $rmaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Rma_Helper_Data $rmaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Adding link to account links block link params if rma
     * is allowed globaly and for current store view
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return Enterprise_Rma_Block_Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if ($this->_rmaData->isEnabled()) {
            /** @var $blockInstance Magento_Page_Block_Template_Links */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
