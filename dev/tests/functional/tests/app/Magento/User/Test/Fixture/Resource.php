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

namespace Magento\User\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * ACL resources fixture
 *
 * @package namespace Magento\User\Test\Fixture
 */
class Resource extends DataFixture
{
    /**
     * @var array Resource id as key and parent id as value
     */
    protected $resources = array(
        'Magento_Adminhtml::dashboard' => null,
        'Magento_PageCache::page_cache' => null,
        'Magento_Sales::sales' => null,
        'Magento_Sales::sales_operation' => 'Magento_Sales::sales',
        'Magento_Sales::sales_order' => 'Magento_Sales::sales_operation',
        'Magento_Sales::actions' => 'Magento_Sales::sales_order',
        'Magento_Sales::create' => 'Magento_Sales::actions',
        'Magento_Reward::reward_spend' => 'Magento_Sales::create',
        'Magento_Sales::actions_view' => 'Magento_Sales::actions',
        'Magento_Sales::email' => 'Magento_Sales::actions',
        'Magento_Sales::reorder' => 'Magento_Sales::actions',
        'Magento_Sales::actions_edit' => 'Magento_Sales::actions',
        'Magento_Sales::cancel' => 'Magento_Sales::actions',
        'Magento_Sales::review_payment' => 'Magento_Sales::actions',
        'Magento_Sales::capture' => 'Magento_Sales::actions',
        'Magento_Sales::invoice' => 'Magento_Sales::actions',
        'Magento_Sales::creditmemo' => 'Magento_Sales::actions',
        'Magento_Sales::hold' => 'Magento_Sales::actions',
        'Magento_Sales::unhold' => 'Magento_Sales::actions',
        'Magento_Sales::ship' => 'Magento_Sales::actions',
        'Magento_Sales::comment' => 'Magento_Sales::actions',
        'Magento_Sales::emails' => 'Magento_Sales::actions',
        'Magento_Sales::sales_invoice' => 'Magento_Sales::sales_operation',
        'Magento_Sales::shipment' => 'Magento_Sales::sales_operation',
        'Magento_Sales::sales_creditmemo' => 'Magento_Sales::sales_operation',
        'Magento_Rma::magento_rma' => 'Magento_Sales::sales_operation',
        'Magento_Sales::billing_agreement' => 'Magento_Sales::sales_operation',
        'Magento_Sales::billing_agreement_actions' => 'Magento_Sales::billing_agreement',
        'Magento_Sales::billing_agreement_actions_view' => 'Magento_Sales::billing_agreement_actions',
        'Magento_Sales::actions_manage' => 'Magento_Sales::billing_agreement_actions',
        'Magento_Sales::use' => 'Magento_Sales::billing_agreement_actions',
        'Magento_Sales::transactions' => 'Magento_Sales::sales_operation',
        'Magento_Sales::transactions_fetch' => 'Magento_Sales::transactions',
        'Magento_Sales::recurring_profile' => 'Magento_Sales::sales_operation',
        'Magento_SalesArchive::archive' => 'Magento_Sales::sales',
        'Magento_SalesArchive::orders' => 'Magento_SalesArchive::archive',
        'Magento_SalesArchive::remove' => 'Magento_SalesArchive::orders',
        'Magento_SalesArchive::add' => 'Magento_SalesArchive::orders',
        'Magento_SalesArchive::invoices' => 'Magento_SalesArchive::archive',
        'Magento_SalesArchive::shipments' => 'Magento_SalesArchive::archive',
        'Magento_SalesArchive::creditmemos' => 'Magento_SalesArchive::archive',
        'Magento_AdvancedCheckout::magento_advancedcheckout' => 'Magento_Sales::sales',
        'Magento_AdvancedCheckout::view' => 'Magento_AdvancedCheckout::magento_advancedcheckout',
        'Magento_AdvancedCheckout::update' => 'Magento_AdvancedCheckout::magento_advancedcheckout',
    );

    /**
     * {@inheritdoc}
     */
    protected function _initData() {}

    /**
     * Just a stub of inherited method
     *
     * @throws \BadMethodCallException
     */
    public function persist()
    {
        throw new \BadMethodCallException('This method is not applicable here. It is data provider for role fixture');
    }
    /**
     * Return requested resource, all it's children and parents
     *
     * @param string $resourceId
     * @throws \InvalidArgumentException
     * @return array
     */
    public function get($resourceId = null)
    {
        if (!array_key_exists($resourceId, $this->resources)) {
            throw new \InvalidArgumentException('No resource "' . $resourceId . '" found');
        }
        $withParents = $this->getParents($resourceId);
        $withParents[] = $resourceId;
        return array_merge($withParents, $this->getChildren($resourceId));
    }

    /**
     * Get all direct parents
     *
     * @param string $resourceId
     * @return array
     */
    protected function getParents($resourceId)
    {
        if (is_null($this->resources[$resourceId])) {
            return array();
        }

        $parents = array();
        $current = $this->resources[$resourceId];

        while (!is_null($this->resources[$current])) {
            $parents[] = $current;
            $current = $this->resources[$current];
        }
        $parents[] = $current;

        return $parents;
    }

    /**
     * Get all child resources
     *
     * @param string $resourceId
     * @return array
     */
    protected function getChildren($resourceId)
    {
        $children = array_keys($this->resources, $resourceId);
        $result = $children;
        foreach ($children as $child) {
            $result = array_merge($result, $this->getChildren($child));
        }
        return $result;
    }
}

