<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\App\Controller;

/**
 * Class AbstractAction
 * @package Magento\Doc\App\Controller
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
