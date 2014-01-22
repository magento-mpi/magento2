<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Pci backend observer model
 *
 * Implements hashes upgrading
 */
namespace Magento\Pci\Model;

use Magento\Event\Observer as EventObserver;

class Observer
{
    /**
     * @var \Magento\Pci\Model\Encryption
     */
    protected $_encryptor;

    /**
     * @param \Magento\Pci\Model\Encryption $encryptor
     */
    public function __construct(\Magento\Pci\Model\Encryption $encryptor)
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
        $model    = $observer->getEvent()->getModel();
        if (!$this->_encryptor->validateHashByVersion($password, $model->getPassword())) {
            $model->changePassword($password, false);
        }
    }
}
