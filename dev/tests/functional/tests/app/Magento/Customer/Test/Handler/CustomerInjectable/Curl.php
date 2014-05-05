<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\CustomerInjectable;

use Magento\Customer\Test\Handler\CustomerInjectable\CustomerInjectableInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;

/**
 * Class Curl
 *
 * @package Magento\Customer\Test\Handler\CustomerInjectable
 */
class Curl extends AbstractCurl implements CustomerInjectableInterface
{

    /**
     * Registration new user
     *
     * @param FixtureInterface $fixture
     * @return mixed|void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_frontend_url'] . 'customer/account/createpost/?nocookie=true';
        $curl = new CurlTransport();
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fixture->getData());
        $curl->read();
        $curl->close();
    }
}
