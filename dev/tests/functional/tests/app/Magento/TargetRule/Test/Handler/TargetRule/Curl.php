<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Handler\TargetRule; 

use Magento\Backend\Test\Handler\Conditions;
use Mtf\Fixture\FixtureInterface;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl handler for creating target rule through backend.
 */
class Curl extends Conditions implements TargetRuleInterface
{
    /**
     * Post request for creating target rule in backend
     *
     * @param FixtureInterface|null $targetRule
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $targetRule = null)
    {
        /** @var TargetRule $targetRule */
        $url = $_ENV['app_backend_url'] . 'admin/targetrule/save';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $data = $targetRule->getData();

        $data['is_active'] = ('Active' == $data['is_active']) ? 1 : 0;
        $data['apply_to'] = $this->getApplyToValue($data['apply_to']);
        $data['use_customer_segment'] = ('All' == $data['use_customer_segment']) ? 0 : 1;
        $data['rule'] = [];
        if (isset($data['conditions_serialized'])) {
            $data['rule']['conditions'] = $this->prepareCondition($data['conditions_serialized']);
            unset($data['conditions_serialized']);
        }
        if (isset($data['actions_serialized'])) {
            $data['rule']['actions'] = $this->prepareCondition($data['actions_serialized']);
            unset($data['actions_serialized']);
        }
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("TargetRule entity creating by curl handler was not successful! Response: $response");
        }

        return [];
//        return ['id' => $this->getCustomerSegmentId($targetRule->getName())];
    }

    /**
     * Get "apply_to" value by label
     *
     * @param string $label
     * @return int
     * @throws \Exception
     */
    protected function  getApplyToValue($label)
    {
        switch ($label) {
            case 'Related Products':
                return 1;
            case 'Up-sells':
                return 2;
            case 'Cross-sells':
                return 3;
        }
        throw new \Exception('Bad value of field "Apply To"');
    }

    /**
     * Get customer segment id by name
     *
     * @param string $name
     * @return int|null
     */
    protected function getCustomerSegmentId($name)
    {
        $filter = ['name' => $name];
        $url = $_ENV['app_backend_url'] . 'customersegment/index/grid/filter/' . $this->encodeFilter($filter);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('/data-column="entity_id"[^>]*>\s*([0-9]+)\s*</', $response, $match);
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
