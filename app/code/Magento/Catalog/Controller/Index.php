<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_redirect('/');
    }
}
