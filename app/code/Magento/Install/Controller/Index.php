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
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Config\Scope $configScope
    ) {
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
