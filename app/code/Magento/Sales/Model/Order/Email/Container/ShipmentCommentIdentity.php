<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Email\Container;

class ShipmentCommentIdentity extends Container implements IdentityInterface
{
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/shipment_comment/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/shipment_comment/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/shipment_comment/identity';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'sales_email/shipment_comment/guest_template';
    const XML_PATH_EMAIL_TEMPLATE = 'sales_email/shipment_comment/template';
    const XML_PATH_EMAIL_ENABLED = 'sales_email/shipment_comment/enabled';

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
