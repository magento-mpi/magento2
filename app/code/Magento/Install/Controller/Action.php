<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller;

class Action extends \Magento\App\Action\Action
{
    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Config\Scope $configScope
     */
    public function __construct(\Magento\App\Action\Context $context, \Magento\Config\Scope $configScope)
    {
        parent::__construct($context);
        $configScope->setCurrentScope('install');
    }
}
