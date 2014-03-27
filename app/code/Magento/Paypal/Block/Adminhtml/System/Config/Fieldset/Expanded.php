<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\System\Config\Fieldset;

use Magento\Data\Form\Element\AbstractElement;

/**
 * Fieldset renderer which expanded by default
 */
class Expanded extends \Magento\Backend\Block\System\Config\Form\Fieldset
{
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\View\Helper\Js $jsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\View\Helper\Js $jsHelper,
        array $data = array()
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Return collapse state
     *
     * @param AbstractElement $element
     * @return string|true
     */
    protected function _isCollapseState($element)
    {
        $extra = $this->_authSession->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        return true;
    }
}
