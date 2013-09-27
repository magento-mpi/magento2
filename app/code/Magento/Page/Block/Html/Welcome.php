<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html page block
 *
 * @category   Magento
 * @package    Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Html_Welcome extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Layout $layout,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_layout = $layout;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get block messsage
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_layout->getBlock('header')->getWelcome();
    }
}
