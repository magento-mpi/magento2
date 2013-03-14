<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_MoneybookersController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Retrieve Moneybookers helper
     *
     * @return Phoenix_Moneybookers_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Phoenix_Moneybookers_Helper_Data');
    }

    /**
     * Send activation Email to Moneybookers
     */
    public function activateemailAction()
    {
        $this->_getHelper()->activateEmail();
    }

    /**
     * Check if email is registered at Moneybookers
     */
    public function checkemailAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            if (empty($params['email'])) {
                Mage::throwException('Error: No parameters specified');
            }
            $response =  $this->_getHelper()->checkEmailRequest($params);
            if (empty($response)) {
                Mage::throwException('Error: Connection to moneybookers.com failed');
            }
            $this->getResponse()->setBody($response);
            return;
        } catch (Mage_Core_Exception $e) {
            $response = $e->getMessage();
        } catch (Exception $e) {
            $response = 'Error: System error during request';
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Check if entered secret is valid
     */
    public function checksecretAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            if (empty($params['email']) || empty($params['secret'])) {
                 Mage::throwException('Error: No parameters specified');
            }
            $response =  $this->_getHelper()->checkSecretRequest($params);
            if (empty($response)) {
                Mage::throwException('Error: Connection to moneybookers.com failed');
            }
            $this->getResponse()->setBody($response);
            return;
        } catch (Mage_Core_Exception $e) {
            $response = $e->getMessage();
        } catch (Exception $e) {
            $response = 'Error: System error during request';
        }
        $this->getResponse()->setBody($response);
    }
}
