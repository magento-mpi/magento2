<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Controller\Adminhtml\Survey;

class Index extends \Magento\AdminNotification\Controller\Adminhtml\Survey
{
    /**
     * Index Action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('isAjax', false)) {
            $this->_objectManager->get('Magento\AdminNotification\Model\Survey')->saveSurveyViewed(true);
        }
        $this->getResponse()->representJson(\Zend_Json::encode(array('survey_decision_saved' => 1)));
    }
}
