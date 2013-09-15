<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Centinel validation form lookup
 */
namespace Magento\Centinel\Block\Authentication;

class Complete extends \Magento\Core\Block\Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare authentication result params and render
     *
     * @return string
     */
    protected function _toHtml()
    {
        $validator = $this->_coreRegistry->registry('current_centinel_validator');
        if ($validator) {
            $this->setIsProcessed(true);
            $this->setIsSuccess($validator->isAuthenticateSuccessful());
        }
        return parent::_toHtml();
    }
}

