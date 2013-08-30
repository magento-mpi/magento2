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
class Magento_Invitation_Block_Customer_Form_Register extends Magento_Customer_Block_Form_Register
{
    /**
     * Retrieve form data
     *
     * @return Magento_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $customerFormData = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerFormData(true);
            $data = new Magento_Object($customerFormData);
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
     * @return Magento_Invitation_Model_Invitation
     */
    public function getCustomerInvitation()
    {
        return Mage::registry('current_invitation');
    }
}
