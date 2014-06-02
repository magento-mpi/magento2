<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Util\Protocol\CurlTransport;
use \Mtf\System\Config;

/**
 * Class TargetRuleEntityTest
 */
abstract class TargetRuleEntityTest extends Injectable
{
    /**
     * @var TargetRule
     */
    protected $targetRule;

    /**
     * Prepare data for tear down
     *
     * @param TargetRule $targetRule
     * @return void
     */
    public function prepareTearDown(
        TargetRule $targetRule
    ) {
        $this->targetRule = $targetRule;
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->targetRule instanceof TargetRule) {
            return;
        }

        $targetRuleId = $this->getTargetRuleId($this->targetRule->getName());
        if ($targetRuleId) {
            $url = $_ENV['app_backend_url'] . 'admin/targetrule/delete/id/' . $targetRuleId;
            $curl = new BackendDecorator(new CurlTransport(), new Config());

            $curl->write(CurlInterface::POST, $url, '1.0');
            $curl->read();
            $curl->close();
        }
    }

    /**
     * Get TargetRule id by name
     *
     * @param string $name
     * @return int|null
     */
    protected function getTargetRuleId($name)
    {
        $filter = ['name' => $name];
        $url = $_ENV['app_backend_url'] . 'admin/targetrule/index/grid/filter/' . $this->encodeFilter($filter);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('/data-column="rule_id"[^>]*>\s*([0-9]+)\s*</', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Encoded filter parameters
     *
     * @param array $filter
     * @return string
     */
    protected function encodeFilter(array $filter)
    {
        $result = [];
        foreach ($filter as $name => $value) {
            $result[] = "{$name}={$value}";
        }
        $result = implode('&', $result);

        return base64_encode($result);
    }
}
