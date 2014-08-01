<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace  Magento\Sales\Model\Order\Email\Container;

use \Magento\Store\Model\Store;

interface IdentityInterface
{
    public function isEnabled();

    /**
     * @return array
     */
    public function getEmailCopyTo();

    public function getCopyMethod();

    public function getGuestTemplateId();

    public function getTemplateId();

    public function getEmailIdentity();

    public function getCustomerEmail();

    public function getCustomerName();

    /**
     * @return Store
     */
    public function getStore();

    public function setStore(Store $store);

    public function setCustomerEmail($email);

    public function setCustomerName($name);
}
