<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class Administrator extends \Magento\Install\Controller\Wizard
{
    /**
     * Install administrator account
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();

        $this->_prepareLayout();
        $this->_view->getLayout()->initMessages();

        $this->_view->getLayout()->addBlock('Magento\Install\Block\Admin', 'install.administrator', 'content');
        $this->_view->renderLayout();
    }
}
