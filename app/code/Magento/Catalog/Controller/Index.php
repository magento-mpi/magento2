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

class Index extends \Magento\Core\Controller\Front\Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_redirect('/');
    }

}
