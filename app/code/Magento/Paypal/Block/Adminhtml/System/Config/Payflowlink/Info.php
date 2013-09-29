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
 * Renderer for Payflow Link information
 */
namespace Magento\Paypal\Block\Adminhtml\System\Config\Payflowlink;

class Info extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'system/config/payflowlink/info.phtml';

    /**
     * Render fieldset html
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $columns = ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='$columns'>" . $this->toHtml() . '</td>');
    }
}
