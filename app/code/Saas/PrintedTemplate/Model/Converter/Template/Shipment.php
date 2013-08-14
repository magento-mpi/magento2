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
 * Shipment converter model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Template_Shipment extends Saas_PrintedTemplate_Model_Converter_Template
{
    /**
     * Sets shippiment and other objects that are necessary for template
     *
     * @param array $args Array with arguments and they should be:
     *     Saas_PrintedTemplate_Model_Template $template
     *     Magento_Sales_Model_Order_Shipment $shipment
     */
    public function __construct(array $data)
    {
        // Check arguments
        if (isset($data['template']) && $data['template'] instanceof Saas_PrintedTemplate_Model_Template
            && isset($data['model']) && $data['model'] instanceof Magento_Sales_Model_Order_Shipment
        ) {
            $template = $data['template'];
            $shipment = $data['model'];
        } else {
            throw new InvalidArgumentException("The constructor's arguments are incorrect.");
        }

        // Loading data that template can require
        $variables = array(
            'shipment' => $shipment,
            'customer' => $shipment->getOrder(),
            'address_billing' => $shipment->getBillingAddress(),
            'order' => $shipment->getOrder(),
            'payment' => $shipment->getOrder()->getPayment(),
        );

        // virtual orders don't have shipping address
        if ($shipment->getShippingAddress()) {
            $variables['address_shipping'] = $shipment->getShippingAddress();
        }

        parent::__construct(array($template, $variables, $shipment->getStoreId()));
    }
}
