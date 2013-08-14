<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Invoice converter model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Template_Invoice extends Saas_PrintedTemplate_Model_Converter_Template
{
    /**
     * Sets invoice and other objects that are necessary for template
     *
     * @param array $args Array with arguments and they should be:
     *     Saas_PrintedTemplate_Model_Template $template
     *     Magento_Sales_Model_Order_Invoice $invoice
     */
    public function __construct(array $data = array())
    {
        // Check arguments
        if (isset($data['template']) && $data['template'] instanceof Saas_PrintedTemplate_Model_Template
            && isset($data['model']) && $data['model'] instanceof Magento_Sales_Model_Order_Invoice
        ) {
            $template = $data['template'];
            $invoice = $data['model'];
        } else {
            throw new InvalidArgumentException("The constructor's arguments are incorrect.");
        }

        // Loading data that template can require
        $variables = array(
            'invoice' => $invoice,
            'customer' => $invoice->getOrder(),
            'address_billing' => $invoice->getBillingAddress(),
            'order' => $invoice->getOrder(),
            'payment' => $invoice->getOrder()->getPayment(),
        );

        // virtual orders don't have shipping address
        if ($invoice->getShippingAddress()) {
            $variables['address_shipping'] = $invoice->getShippingAddress();
        }

        parent::__construct(array($template, $variables, $invoice->getStoreId()));
    }
}
