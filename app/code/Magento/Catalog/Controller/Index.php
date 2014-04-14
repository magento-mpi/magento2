<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller;

class Index extends \Magento\App\Action\Action
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
