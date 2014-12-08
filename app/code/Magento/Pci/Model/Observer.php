<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Model;

use Magento\Framework\Event\Observer as EventObserver;

/**
 * Pci backend observer model
 *
 * Implements hashes upgrading
 */
class Observer
{
    /**
     * Pci encryption model
     *
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(\Magento\Framework\Encryption\EncryptorInterface $encryptor)
    {
        $this->_encryptor = $encryptor;
    }

    /**
     * Upgrade customer password hash when customer has logged in
     *
     * @param EventObserver $observer
     * @return void
     */
    public function upgradeCustomerPassword($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $model = $observer->getEvent()->getModel();
        if (!$this->_encryptor->validateHashByVersion($password, $model->getPasswordHash())) {
            $model->changePassword($password);
        }
    }
}
