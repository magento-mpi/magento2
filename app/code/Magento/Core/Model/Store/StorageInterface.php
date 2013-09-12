<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Core_Model_Store_StorageInterface extends Magento_Core_Model_Store_ListInterface
{
    /**
     * Initialize current application store
     */
    public function initCurrentStore();
}
