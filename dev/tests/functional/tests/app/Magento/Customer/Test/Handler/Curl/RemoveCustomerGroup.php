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

namespace Magento\Customer\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

class RemoveCustomerGroup extends Curl
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $deleteUrl = 'customer/group/delete/id/%s/';

    /**
     * Execute handler
     *
     * @param Fixture $fixture [optional]
     * @return mixed
     */
    public function execute(Fixture $fixture = null)
    {
        /** @var \Magento\Customer\Test\Fixture\VatGroup $fixture*/
        $groups = $fixture->getGroupsIds();
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $response = '';
        foreach ($groups as $groupId) {
            $url = sprintf($_ENV['app_backend_url'] . $this->deleteUrl, $groupId);
            $curl->write(CurlInterface::GET, $url, '1.0', array());
            $response = $curl->read();
        }
        $curl->close();
        return $response;
    }
}
