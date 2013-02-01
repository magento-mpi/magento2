<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Mage_Core_Model_Store_StorageInterface extends Mage_Core_Model_Store_ListInterface
{
    /**
     * Initialize current application store
     */
    public function initCurrentStore();
}
