<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Survey Action
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Survey extends Magento_Adminhtml_Controller_Action
{
    /**
     * Index Action
     *
     */
    public function indexAction()
    {
        if ($this->getRequest()->getParam('isAjax', false)) {
            $this->_objectManager->get('Magento_AdminNotification_Model_Survey')->saveSurveyViewed(true);
        }
        $this->getResponse()->setBody(Zend_Json::encode(array('survey_decision_saved' => 1)));
    }

    /**
     * Check if user has enough privileges
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(null);
    }
}
