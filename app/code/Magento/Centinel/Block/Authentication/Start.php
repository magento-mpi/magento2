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
 * Authentication start/redirect form
 */
namespace Magento\Centinel\Block\Authentication;

class Start extends \Magento\Core\Block\Template
{
    /**
     * Prepare form parameters and render
     *
     * @return string
     */
    protected function _toHtml()
    {
        $validator = \Mage::registry('current_centinel_validator');
        if ($validator && $validator->shouldAuthenticate()) {
            $this->addData($validator->getAuthenticateStartData());
            return parent::_toHtml();
        }
        return '';
    }
}

