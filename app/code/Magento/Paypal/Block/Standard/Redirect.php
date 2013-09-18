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
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_elementFactory;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Data_Form_Element_Factory $elementFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Data_Form_Element_Factory $elementFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Context $context,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_formFactory = $formFactory;
        $this->_elementFactory = $elementFactory;
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
        $idSuffix = $this->_coreData->uniqHash();
        $submitButton = $this->_elementFactory->create('submit', array('attributes' => array(
            'value'    => __('Click here if you are not redirected within 10 seconds.'),
        )));
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
