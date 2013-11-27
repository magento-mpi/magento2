<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Repository;

use Mtf\Repository\AbstractRepository;
/**
 * Class Abstract Repository
 *
 * @package namespace Magento\User\Test\Repository
 */
class Resource extends AbstractRepository
{
    /**
     * @var array
     */
    protected $_config = array();

    /**
     * Resource groups according to their labels
     * @var array
     */
    protected $_groups = array('Sales');

    /**
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['role_sales'] = $this->getRoleSales();
    }

    private function getRoleSales()
    {
        $role['data']['fields']['resource']['value'] = $this->_getSales();

        return array_replace_recursive($this->_data['default'], $role);
    }

    /**
     * @return array
     */
    protected function _getSales()
    {
        return array(
            'Magento_Sales::sales',
            'Magento_Sales::sales_operation',
            'Magento_Sales::sales_order',
            'Magento_Sales::actions',
            'Magento_Sales::create',
            'Magento_Reward::reward_spend',
            'Magento_Sales::actions_view',
            'Magento_Sales::email',
            'Magento_Sales::reorder',
            'Magento_Sales::actions_edit',
            'Magento_Sales::cancel',
            'Magento_Sales::review_payment',
            'Magento_Sales::capture',
            'Magento_Sales::invoice',
            'Magento_Sales::creditmemo',
            'Magento_Sales::hold',
            'Magento_Sales::unhold',
            'Magento_Sales::ship',
            'Magento_Sales::comment',
            'Magento_Sales::emails',
            'Magento_Sales::sales_invoice',
            'Magento_Sales::shipment',
            'Magento_Sales::sales_creditmemo',
            'Magento_Rma::magento_rma',
            'Magento_Sales::billing_agreement',
            'Magento_Sales::billing_agreement_actions',
            'Magento_Sales::billing_agreement_actions_view',
            'Magento_Sales::actions_manage',
            'Magento_Sales::use',
            'Magento_Sales::transactions',
            'Magento_Sales::transactions_fetch',
            'Magento_Sales::recurring_profile',
            'Magento_SalesArchive::archive',
            'Magento_SalesArchive::orders',
            'Magento_SalesArchive::remove',
            'Magento_SalesArchive::add',
            'Magento_SalesArchive::invoices',
            'Magento_SalesArchive::shipments',
            'Magento_SalesArchive::creditmemos',
            'Magento_AdvancedCheckout::magento_advancedcheckout',
            'Magento_AdvancedCheckout::view',
            'Magento_AdvancedCheckout::update'
        );
    }
}
