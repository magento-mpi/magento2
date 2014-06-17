<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Handler\AdminUser;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;
use Magento\Backend\Test\Handler\Extractor;

/**
 * Class Curl
 * Creates Admin User Entity
 */
class Curl extends AbstractCurl implements AdminUserInterface
{
    /**
     * Curl creation of Admin User
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var \Magento\User\Test\Fixture\AdminUserInjectable $fixture */
        $data = $fixture->getData();
        $role = $fixture->getDataFieldConfig('role')['source']->getRole();
        $data['roles[]'] = $role->getRoleId();

        $url = $_ENV['app_backend_url'] . 'admin/user/save/active_tab/main_section/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Admin user entity creating by curl handler was not successful! Response: $response");
        }

        $url = 'admin/user/roleGrid/sort/user_id/dir/desc';
        $regExpPattern = '/class=\"\scol\-id col\-user_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
            . $data['username'] . '/siu';
        $extractor = new Extractor($url, $regExpPattern);

        return ['user_id' => $extractor->getData()[1]];
    }
}
