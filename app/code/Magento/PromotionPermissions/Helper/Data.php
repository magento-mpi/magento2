<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PromotionPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Promotion Permissions Data Helper
 *
 * @category    Magento
 * @package     Magento_PromotionPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PromotionPermissions_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Path to node in ACL that specifies edit permissions for catalog rules
     *
     * Used to check if admin has permission to edit catalog rules
     */
    const EDIT_PROMO_CATALOGRULE_ACL_PATH = 'Magento_PromotionPermissions::edit';

    /**
     * Path to node in ACL that specifies edit permissions for sales rules
     *
     * Used to check if admin has permission to edit sales rules
     */
    const EDIT_PROMO_SALESRULE_ACL_PATH = 'Magento_PromotionPermissions::quote_edit';

    /**
     * Path to node in ACL that specifies edit permissions for reminder rules
     *
     * Used to check if admin has permission to edit reminder rules
     */
    const EDIT_PROMO_REMINDERRULE_ACL_PATH = 'Magento_PromotionPermissions::magento_reminder_edit';

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(Magento_Core_Helper_Context $context, \Magento\AuthorizationInterface $authorization)
    {
        parent::__construct($context);
        $this->_authorization = $authorization;
    }

    /**
     * Check if admin has permissions to edit catalog rules
     *
     * @return boolean
     */
    public function getCanAdminEditCatalogRules()
    {
        return (boolean) $this->_authorization->isAllowed(self::EDIT_PROMO_CATALOGRULE_ACL_PATH);
    }

    /**
     * Check if admin has permissions to edit sales rules
     *
     * @return boolean
     */
    public function getCanAdminEditSalesRules()
    {
        return (boolean) $this->_authorization->isAllowed(self::EDIT_PROMO_SALESRULE_ACL_PATH);
    }

    /**
     * Check if admin has permissions to edit reminder rules
     *
     * @return boolean
     */
    public function getCanAdminEditReminderRules()
    {
        return (boolean) $this->_authorization->isAllowed(self::EDIT_PROMO_REMINDERRULE_ACL_PATH);
    }
}
