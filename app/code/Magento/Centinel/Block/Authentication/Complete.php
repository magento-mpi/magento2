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
     * Prepare authentication result params and render
     *
     * @return string
     */
    protected function _toHtml()
    {
        $validator = \Mage::registry('current_centinel_validator');
        if ($validator) {
            $this->setIsProcessed(true);
            $this->setIsSuccess($validator->isAuthenticateSuccessful());
        }
        return parent::_toHtml();
    }
}

