<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Handler\Curl;

use Exception;
use Mtf\Fixture;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;
use Mtf\Handler\Curl;
use Magento\SalesRule\Test\Fixture\SalesRule;

/**
 * Class DeleteSalesRule
 *
 * @package Magento\SalesRule\Test\Handler\Curl
 */
class DeleteSalesRule extends Curl
{
    /**
     * Post request for deleting a sales rule by id from backend
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        if ($fixture instanceof SalesRule && $fixture->getSalesRuleId() !== null) {
            $url = $_ENV['app_backend_url'] . 'sales_rule/promo_quote/delete/id/' . $fixture->getSalesRuleId() . '/';
            $curl = new BackendDecorator(new CurlTransport(), new Config());
            $curl->addOption(CURLOPT_HEADER, 1);
            $curl->write(CurlInterface::GET, $url);
            $curl->read();
            $curl->close();
        } else {
            throw new Exception('Pass in DeleteSalesRule Fixture with defined id');
        }
    }
}
