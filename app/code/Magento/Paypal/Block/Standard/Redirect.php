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

class Redirect extends \Magento\View\Element\AbstractBlock
{
    /**
     * @var \Magento\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Paypal\Model\StandardFactory
     */
    protected $_paypalStandardFactory;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Paypal\Model\StandardFactory $paypalStandardFactory
     * @param \Magento\Math\Random $mathRandom
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Data\Form\Element\Factory $elementFactory,
        \Magento\Paypal\Model\StandardFactory $paypalStandardFactory,
        \Magento\Math\Random $mathRandom,
        array $data = array()
    ) {
        $this->_formFactory = $formFactory;
        $this->_elementFactory = $elementFactory;
        $this->_paypalStandardFactory = $paypalStandardFactory;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $standard = $this->_paypalStandardFactory->create();

        $form = $this->_formFactory->create();
        $form->setAction(
            $standard->getConfig()->getPaypalUrl()
        )->setId(
            'paypal_standard_checkout'
        )->setName(
            'paypal_standard_checkout'
        )->setMethod(
            'POST'
        )->setUseContainer(
            true
        );
        foreach ($standard->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        $idSuffix = $this->mathRandom->getUniqueHash();
        $submitButton = $this->_elementFactory->create(
            'submit',
            array('data' => array('value' => __('Click here if you are not redirected within 10 seconds.')))
        );
        $id = "submit_to_paypal_button_{$idSuffix}";
        $submitButton->setId($id);
        $form->addElement($submitButton);
        $html = '<html><body>';
        $html .= __('You will be redirected to the PayPal website in a few seconds.');
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">document.getElementById("paypal_standard_checkout").submit();';
        $html .= '</script></body></html>';

        return $html;
    }
}
