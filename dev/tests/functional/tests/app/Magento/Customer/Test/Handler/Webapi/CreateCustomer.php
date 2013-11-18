<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\Webapi;

use Mtf\Fixture;
use Mtf\Handler\Webapi;
use Mtf\Util\Protocol\SoapTransport;

/**
 * Class CreateCustomer
 *
 * @package Magento\Customer\Handler\Webapi
 */
class CreateCustomer extends Webapi
{
    /**
     * Create customer through request
     *
     * @param Fixture $fixture [optional]
     * @return mixed
     */
    public function execute(Fixture $fixture = null)
    {
        $configuration = $this->_configuration->getConfigParam('handler/webapi');

        $soap = new SoapTransport($configuration['soap']);
        return $soap->call('customerCustomerList', $fixture->getData());
    }
}
