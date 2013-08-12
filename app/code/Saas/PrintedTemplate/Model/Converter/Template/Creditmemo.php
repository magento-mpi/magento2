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
 * Creditmemo converter model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Template_Creditmemo extends Saas_PrintedTemplate_Model_Converter_Template
{
    /**
     * Sets creditmemo and other objects that are necessary for template
     *
     * @param array $data Array with arguments and they should be:
     *     Saas_PrintedTemplate_Model_Template $template
     *     Magento_Sales_Model_Order_Creditmemo $creditmemo
     */
    public function __construct(array $data = array())
    {
        // Check arguments
        if (isset($data['template']) && $data['template'] instanceof Saas_PrintedTemplate_Model_Template
            && isset($data['model']) && $data['model'] instanceof Magento_Sales_Model_Order_Creditmemo
        ) {
            $template = $data['template'];
            $creditmemo = $data['model'];
        } else {
            throw new InvalidArgumentException('The constructor\'s arguments are incorrect.');
        }

        // Loading data that template can require
        $variables = array(
            'creditmemo' => $creditmemo,
            'customer' => $creditmemo->getOrder(),
            'address_billing' => $creditmemo->getBillingAddress(),
            'order' => $creditmemo->getOrder(),
            'payment' => $creditmemo->getOrder()->getPayment(),
        );

        // virtual orders don't have shipping address
        if ($creditmemo->getShippingAddress()) {
            $variables['address_shipping'] = $creditmemo->getShippingAddress();
        }

        parent::__construct(array($template, $variables, $creditmemo->getStoreId()));
    }
}
