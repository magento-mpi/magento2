<?php
/**
 * Customer session storage
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Session;

class Storage extends \Magento\Session\Storage
{
    /**
     * @param \Magento\Customer\Model\Config\Share $configShare
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param string $namespace
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        $namespace = 'customer',
        array $data = array()
    ) {
        if ($configShare->isWebsiteScope()) {
            $namespace .= '_' . ($storeManager->getWebsite()->getCode());
        }
        parent::__construct($namespace, $data);
    }
}

