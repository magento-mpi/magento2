<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Survey Action
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_SurveyController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index Action
     *
     */
    public function indexAction()
    {
        if ($this->getRequest()->getParam('isAjax', false)) {
            Mage_AdminNotification_Model_Survey::saveSurveyViewed(true);
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
