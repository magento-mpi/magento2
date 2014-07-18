<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class LocaleChange extends \Magento\Install\Controller\Wizard
{
    /**
     * Change current locale
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();

        $locale = $this->getRequest()->getParam('locale');
        $timezone = $this->getRequest()->getParam('timezone');
        $currency = $this->getRequest()->getParam('currency');
        if ($locale) {
            $this->_session->setLocale($locale)->setTimezone($timezone)->setCurrency($currency);
        }

        $this->_redirect('*/*/locale');
    }
}
