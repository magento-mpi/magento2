<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fielset renderer which expanded by default
 */
namespace Magento\Paypal\Block\Adminhtml\System\Config\Fieldset;

class Expanded
    extends \Magento\Backend\Block\System\Config\Form\Fieldset
{
    /**
     * Return collapse state
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        $extra = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        return true;
    }
}
