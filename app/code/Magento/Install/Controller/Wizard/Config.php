<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class Config extends \Magento\Install\Controller\Wizard
{
    /**
     * Configuration data installation
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();
        $this->_getInstaller()->checkServer();

        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);

        $data = $this->getRequest()->getQuery('config');
        if ($data) {
            $this->_session->setLocaleData($data);
        }

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getLayout()->addBlock('Magento\Install\Block\Config', 'install.config', 'content');

        $this->_view->renderLayout();
    }
}
