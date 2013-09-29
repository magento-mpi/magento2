<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Store;

interface StorageInterface extends \Magento\Core\Model\Store\ListInterface
{
    /**
     * Initialize current application store
     */
    public function initCurrentStore();
}
