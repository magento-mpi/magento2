<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Handler\AdminUserRole;

use Magento\Backend\Test\Handler\Extractor;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Creates Admin User role
 */
class Curl extends AbstractCurl implements AdminUserRoleInterface
{
    /**
     * Curl creation of Admin User Role
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        $data['rolename'] = $data['role_name'];
        unset($data['role_name']);
        $data['all'] = ($data['resource_access'] == 'All') ? '1' : '0';
        unset($data['resource_access']);
        $data['gws_is_all'] = (isset($data['gws_is_all'])) ? $data['gws_is_all'] : '1';
        $url = $_ENV['app_backend_url'] . 'admin/user_role/saverole/active_tab/info/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Role creating by curl handler was not successful! Response: $response");
        }

        $url = 'admin/user_role/roleGrid/sort/role_id/dir/desc/';
        $regExpPattern = '/class=\"\scol\-id col\-role_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
            . $data['rolename'] . '/siu';

        $extractor = new Extractor($url, $regExpPattern);

        return ['role_id' => $extractor->getData()[1]];
    }
}
