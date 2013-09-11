<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer registration form block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Customer\Form;

class Register extends \Magento\Customer\Block\Form\Register
{
    /**
     * Retrieve form data
     *
     * @return \Magento\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $customerFormData = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerFormData(true);
            $data = new \Magento\Object($customerFormData);
            if (empty($customerFormData)) {
                $invitation = $this->getCustomerInvitation();

                if ($invitation->getId()) {
                    // check, set invitation email
                    $data->setEmail($invitation->getEmail());
                }
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }


    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/createpost', array('_current'=>true));
    }

    /**
     * Retrieve customer invitation
     *
     * @return \Magento\Invitation\Model\Invitation
     */
    public function getCustomerInvitation()
    {
        return \Mage::registry('current_invitation');
    }
}
