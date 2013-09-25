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

class WebapiDocBlock extends Magento_TestFramework_Bootstrap_DocBlock
{
    /**
     * Get list of subscribers. In addition, register <b>magentoApiDataFixture</b> annotation processing.
     *
     * @param Magento_TestFramework_Application $application
     * @return array
     */
    protected function _getSubscribers(Magento_TestFramework_Application $application)
    {
        $subscribers = parent::_getSubscribers($application);
        array_unshift($subscribers, new \Magento\TestFramework\Annotation\ApiDataFixture("{$this->_fixturesBaseDir}/api"));
        return $subscribers;
    }
}
