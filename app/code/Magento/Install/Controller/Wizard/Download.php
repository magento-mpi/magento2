<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class Download extends \Magento\Install\Controller\Wizard
{
    /**
     * Download page action
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();
        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getLayout()->addBlock('Magento\Install\Block\Download', 'install.download', 'content');

        $this->_view->renderLayout();
    }
}
