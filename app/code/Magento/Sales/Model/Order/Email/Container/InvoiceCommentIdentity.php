<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Model\Order\Email\Container;

class InvoiceCommentIdentity extends Container implements IdentityInterface
{
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/invoice_comment/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/invoice_comment/copy_to';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'sales_email/invoice_comment/guest_template';
    const XML_PATH_EMAIL_TEMPLATE = 'sales_email/invoice_comment/template';
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/invoice_comment/identity';
    const XML_PATH_EMAIL_ENABLED = 'sales_email/invoice_comment/enabled';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
