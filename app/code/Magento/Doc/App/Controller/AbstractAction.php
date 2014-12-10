<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\App\Controller;

/**
 * Class AbstractAction
 * @package Magento\Doc\App\Controller
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * Ensure full access for all
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
