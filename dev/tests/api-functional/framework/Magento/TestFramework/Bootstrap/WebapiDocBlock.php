<?php
/**
 * Bootstrap of the custom Web API DocBlock annotations.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Bootstrap;

class WebapiDocBlock extends \Magento\TestFramework\Bootstrap\DocBlock
{
    /**
     * Get list of subscribers. In addition, register <b>magentoApiDataFixture</b> annotation processing.
     *
     * @param \Magento\TestFramework\Application $application
     * @return array
     */
    protected function _getSubscribers(\Magento\TestFramework\Application $application)
    {
        $subscribers = parent::_getSubscribers($application);
        array_unshift(
            $subscribers,
            new \Magento\TestFramework\Annotation\ApiDataFixture($this->_fixturesBaseDir)
        );
        return $subscribers;
    }
}
