<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class End extends \Magento\Install\Controller\Wizard
{
    /**
     * End installation
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();

        if ($this->_appState->isInstalled()) {
            $this->_redirect('*/*');
            return;
        }

        $this->_getInstaller()->finish();

        $this->_objectManager->get('Magento\Install\Model\Survey')->saveSurveyViewed(true);

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();

        $this->_view->getLayout()->addBlock('Magento\Install\Block\End', 'install.end', 'content');
        $this->_view->renderLayout();
        $this->_session->clearStorage();
    }
}
