<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Google Content Captcha challenge
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Block_Adminhtml_Captcha extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'captcha.phtml';

    /**
     * Get HTML code for confirm captcha button
     *
     * @return string
     */
    public function getConfirmButtonHtml()
    {
        $confirmButton = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'     => $this->__('Confirm'),
                'onclick'   => "if($('user_confirm').value != '')
                                {
                                    setLocation('".$this->getUrl('*/*/confirmCaptcha', array('_current'=>true))."' + 'user_confirm/' + $('user_confirm').value + '/');
                                }",
                'class'     => 'task'
            ));
        return $confirmButton->toHtml();
    }
}
