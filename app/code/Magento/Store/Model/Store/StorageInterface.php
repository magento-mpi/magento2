<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model\Store;

interface StorageInterface extends \Magento\Core\Model\Store\ListInterface
{
    /**
     * Initialize current application store
     *
     * @return void
     */
    public function initCurrentStore();
}
