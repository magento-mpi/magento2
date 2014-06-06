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
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Active' => 1,
            'Inactive' => 0
        ],
        'apply_to' => [
            'Related Products' => 1,
            'Up-sells' => 2,
            'Cross-sells' => 3
        ],
        'use_customer_segment' => [
            'All' => 0,
            'Specified' => 1
        ]
    ];

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
        $url = $_ENV['app_backend_url']
            . 'admin/targetrule/save/back/edit/active_tab/magento_targetrule_edit_tab_main/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $data = $this->replaceMappingData($targetRule->getData());

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

        return ['id' => $this->getTargetRuleId($response)];
    }

    /**
     * Get target rule id from response
     *
     * @param string $response
     * @return int|null
     */
    protected function getTargetRuleId($response)
    {
        preg_match('/targetrule\/delete\/id\/([0-9]+)/', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }
}
