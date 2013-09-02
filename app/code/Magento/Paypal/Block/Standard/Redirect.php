<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Paypal_Block_Standard_Redirect extends Magento_Core_Block_Abstract
{
    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        array $data = array()
    ) {
        $this->_formFactory = $formFactory;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $standard = Mage::getModel('Magento_Paypal_Model_Standard');

        $form = $this->_formFactory->create();
        $form->setAction($standard->getConfig()->getPaypalUrl())
            ->setId('paypal_standard_checkout')
            ->setName('paypal_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->getStandardCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $idSuffix = Mage::helper('Magento_Core_Helper_Data')->uniqHash();
        $submitButton = new Magento_Data_Form_Element_Submit(array(
            'value'    => __('Click here if you are not redirected within 10 seconds.'),
        ));
        $id = "submit_to_paypal_button_{$idSuffix}";
        $submitButton->setId($id);
        $form->addElement($submitButton);
        $html = '<html><body>';
        $html.= __('You will be redirected to the PayPal website in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("paypal_standard_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}
