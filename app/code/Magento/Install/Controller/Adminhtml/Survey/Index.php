<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Adminhtml\Survey;

class Index extends \Magento\Install\Controller\Adminhtml\Survey
{
    /**
     * Index Action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('isAjax', false)) {
            $this->_objectManager->get('Magento\Install\Model\Survey')->saveSurveyViewed(true);
        }
        $this->getResponse()->representJson(\Zend_Json::encode(array('survey_decision_saved' => 1)));
    }
}
