<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement view plane
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Form extends Mage_Adminhtml_Block_Template
{
    /**
     * Define custom template
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('billing/agreement/view/form.phtml');
    }
}
