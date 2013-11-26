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

namespace Magento\User\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class CreateCategory.
 * Curl handler for creating category.
 *
 * @package Magento\Catalog\Test\Handler\Curl
 */
class CreateRole extends Curl
{
    /**
     * @param array $fields
     * @return array
     */
    protected function _preparePostData(array $fields)
    {
        $data = array();
        foreach ($fields as $key => $value) {
            $data[$key] = $value['value'];
        }
        return $data;
    }


    /**
     * Execute handler
     *
     * @param Fixture|null $fixture [optional]
     * @throws UnexpectedValueException
     * @return mixed
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'admin/user_role/saverole/';
        $data = $this->_preparePostData($fixture->getData('fields'));

        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        preg_match("/You\ saved\ the\ role\./", $response, $matches);
        if (empty($matches)) {
            throw new UnexpectedValueException('Success confirmation message not found');
        }

        preg_match('/class=\"a\-right col\-role_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
            . $data['rolename'] . '/siu', $response, $matches);
        if (empty($matches)) {
            throw new UnexpectedValueException('Cannot find role id');
        }
        $data['id'] = $matches[1];
        return $data;
    }

}