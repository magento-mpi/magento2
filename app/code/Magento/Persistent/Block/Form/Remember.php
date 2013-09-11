<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Remember Me block
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Persistent\Block\Form;

class Remember extends \Magento\Core\Block\Template
{
    /**
     * Prevent rendering if Persistent disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $helper \Magento\Persistent\Helper\Data */
        $helper = \Mage::helper('Magento\Persistent\Helper\Data');
        return ($helper->isEnabled() && $helper->isRememberMeEnabled()) ? parent::_toHtml() : '';
    }

    /**
     * Is "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        /** @var $helper \Magento\Persistent\Helper\Data */
        $helper = \Mage::helper('Magento\Persistent\Helper\Data');
        return $helper->isEnabled() && $helper->isRememberMeEnabled() && $helper->isRememberMeCheckedDefault();
    }
}
