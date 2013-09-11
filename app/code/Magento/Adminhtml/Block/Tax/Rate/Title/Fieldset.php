<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Rate Titles Fieldset
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Tax\Rate\Title;

class Fieldset extends \Magento\Data\Form\Element\Fieldset
{
    public function getBasicChildrenHtml()
    {
        return \Mage::getBlockSingleton('Magento\Adminhtml\Block\Tax\Rate\Title')->toHtml();
    }
}
