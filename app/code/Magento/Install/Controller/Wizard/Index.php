<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class Index extends \Magento\Install\Controller\Wizard
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_redirect('*/*/begin');
    }
}
