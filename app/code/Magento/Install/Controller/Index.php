<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Install index controller
 */
namespace Magento\Install\Controller;

class Index extends \Magento\Install\Controller\Action
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Config\Scope $configScope
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Config\Scope $configScope)
    {
        parent::__construct($context, $configScope);
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_redirect('install/wizard/begin');
    }
}
