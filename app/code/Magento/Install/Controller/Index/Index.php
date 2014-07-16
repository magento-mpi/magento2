<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Config\Scope $configScope
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Config\Scope $configScope
    ) {
        parent::__construct($context);
        $configScope->setCurrentScope('install');
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_redirect('install/wizard/begin');
    }
}
