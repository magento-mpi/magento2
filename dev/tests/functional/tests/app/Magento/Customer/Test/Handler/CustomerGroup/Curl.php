<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\CustomerGroup;

use Magento\Customer\Test\Handler\CustomerGroup\CustomerGroupInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;

use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 *
 * @package CustomerGroup
 */
class Curl extends AbstractCurl implements CustomerGroupInterface
{
    /**
     * @var string
     */
    private $url = 'http://magentotest.me/index.php/backend/tax/tax/ajaxSave/?isAjax=true';

    /**
     * Prepare POST data for creating tax class request
     *
     * @param FixtureInterface $fixture
     * @return mixed|void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = [
            'class_name' => $fixture->getData('tax_class'),
            'class_type' => 'CUSTOMER',
        ];

        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $this->url, '1.0', array(), $data);
        $curl->read();
        $curl->close();

    }
}
