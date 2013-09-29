<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface needed to be implemented by controller that wants to
 * show product view page
 */
namespace Magento\Catalog\Controller\Product\View;

interface ViewInterface
{
    /**
     * Loads layout messages from message storage
     *
     * @param string $messagesStorage
     */
    public function initLayoutMessages($messagesStorage);
}
