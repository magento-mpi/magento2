<?php
/**
 * Session storage interface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Sesstion
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Session;

interface StorageInterface
{
    /**
     * Initialize storage data
     *
     * @param array $data
     * @return $this
     */
    public function init(array $data);

    /**
     * Get current storage namespace
     *
     * @return string
     */
    public function getNamespace();
}
