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

class Action extends \Magento\Framework\App\Action\Action
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Config\Scope $configScope
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Config\Scope $configScope)
    {
        parent::__construct($context);
        $configScope->setCurrentScope('install');
    }
}
