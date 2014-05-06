<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Handler\AdminUserRole;

use Magento\User\Test\Handler\AdminUserRole\AdminUserRoleInterface;
use Magento\Backend\Test\Handler\Pagination;
use Magento\User\Test\Page\Adminhtml\UserRoleIndex;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 *
 * @package Magento\User\Test\Handler\AdminUserRole
 */
class Curl extends AbstractCurl implements AdminUserRoleInterface
{
    /**
     * Default attribute values for fixture
     *
     * @var array
     */
    protected $defaultAttributeValues = [
        'gws_is_all' => 1,
        'all' => 1,
    ];

    /**
     * Curl creation of Admin User Role
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $fixtureData = $fixture->getData();
        $data['rolename'] = $fixtureData['role_name'];
        foreach ($this->defaultAttributeValues as $key => $value) {
            $data[$key] = $value;
        }
        $url = $_ENV['app_backend_url'] . 'admin/user_role/saverole/active_tab/info/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Admin user role entity creating by curl handler was not successful! Response: $response");
        }

        $paginationParams = [
            'url' => 'admin/user_role/roleGrid/sort/role_id/dir/desc/',
            'pattern' => '/class=\"\scol\-id col\-role_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
                . $data['rolename'] . '/siu'
        ];
        $pagination = new Pagination();
        $pagination->prepare($paginationParams);

        return ['role_id' => $pagination->getId()];
    }
}
