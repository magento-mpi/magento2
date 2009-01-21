<?php

class Mage_AmazonPayments_Block_Cba_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $cba = Mage::getModel('amazonpayments/payment_cba');

        $form = new Varien_Data_Form();
        $form->setAction($cba->getAmazonRedirectUrl())
            ->setId('amazonpayments_cba')
            ->setName('amazonpayments_cba')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($cba->getCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Checkout by Amazon in a few seconds.');
        $html.= $form->toHtml();
        #$html.= '<script type="text/javascript">document.getElementById("amazonpayments_cba").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}