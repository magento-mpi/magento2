<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class AdminUserRole
 * Predefined dataSets provider for UserRoles entity
 */
class AdminUserRole extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     * @constructor
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'rolename' => 'RoleName%isolation%',
            'resource_access' => 'All'
        ];

        $this->_data['role_sales'] = [
            'rolename' => 'RoleName%isolation%',
            'resource_access' => 'Custom',
            'roles_resources' => [
                'Sales' => 'Magento_Sales::sales',
                'Operation' => 'Magento_Sales::sales_operation',
                'Actions' => 'Magento_Sales::actions',
                'Orders' => 'Magento_Sales::sales_order',
                'Create' => 'Magento_Sales::create',
                'Can Spend Reward Points' => 'Magento_Reward::reward_spend',
                'View' => 'Magento_Sales::actions_view',
                'Send Order Email' => 'Magento_Sales::email',
                'Reorder' => 'Magento_Sales::reorder',
                'Edit' => 'Magento_Sales::actions_edit',
                'Cancel' => 'Magento_Sales::cancel',
                'Accept or Deny Payment' => 'Magento_Sales::review_payment',
                'Capture' => 'Magento_Sales::capture',
                'Invoice' => 'Magento_Sales::invoice',
                'Credit Memos' => 'Magento_Sales::creditmemo',
                'Hold' => 'Magento_Sales::hold',
                'Unhold' => 'Magento_Sales::unhold',
                'Ship' => 'Magento_Sales::ship',
                'Comment' => 'Magento_Sales::comment',
                'Send Sales Emails' => 'Magento_Sales::emails',
            ]
        ];
    }
}
