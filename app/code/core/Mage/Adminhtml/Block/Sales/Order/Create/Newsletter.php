<?php
/**
 * Adminhtml sales order create newsletter block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Newsletter extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_newsletter');
    }

    public function getHeaderText()
    {
        return __('Newsletter Subscription');
    }

    public function getHeaderCssClass()
    {
        return 'head-newsletter-list';
    }

    protected function _initChildren()
    {
        $this->setChild('form', $this->getLayout()->createBlock('adminhtml/sales_order_create_newsletter_form'));
        return parent::_initChildren();
    }

    public function toHtml()
    {
        if (intval($this->getCustomerId())) {
            return parent::toHtml();
        }
        return '';
    }

}
