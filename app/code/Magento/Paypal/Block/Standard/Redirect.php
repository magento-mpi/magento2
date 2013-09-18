<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Standard;

class Redirect extends \Magento\Core\Block\AbstractBlock
{
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @var \Magento\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Data\FormFactory $formFactory,
        \Magento\Data\Form\Element\Factory $elementFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Context $context,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_formFactory = $formFactory;
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $standard = \Mage::getModel('Magento\Paypal\Model\Standard');

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
