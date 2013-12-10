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

namespace Magento\Rma\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Fixture with all necessary data for creating a return item on the frontend
 *
 * @package Magento\Rma\Test\Fixture
 */
class OrderSearch extends DataFixture
{
    /**
     * Order id
     *
     * @var string
     */
    protected $orderId;

    /**
     * Billing Lastname
     *
     * @var string
     */
    protected $billingLastname;

    /**
     * Email Address
     *
     * @var string
     */
    protected $emailAddress;

    /**
     * Find Order By
     *
     * @var string
     */
    protected $findOrderBy;

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        //
    }

    /**
     * Returns the order id for this order search instance
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Returns the billing lastname for this order search instance
     */
    public function getBillingLastname()
    {
        return $this->billingLastname;
    }

    /**
     * Returns the email address for this order search instance

     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Returns the find order by for this order search instance

     */
    public function getFindOrderBy()
    {
        return $this->findOrderBy;
    }

    /**
     * Set the order id for this order search instance
     *
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Set the billing lastname for this order search instance
     *
     * @param string $billingLastname
     */
    public function setBillingLastname($billingLastname)
    {
        $this->billingLastname = $billingLastname;
    }

    /**
     * Set the email address for this order search instance
     *
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * Set the email address for this order search instance
     *
     * @param string $findOrderBy
     */
    public function setFindOrderBy($findOrderBy)
    {
        $this->findOrderBy = $findOrderBy;
    }
}
