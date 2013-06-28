<?php
/**
 * Bootstrap of the custom Web API DocBlock annotations.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Bootstrap_WebapiDocBlock extends Magento_Test_Bootstrap_DocBlock
{
    protected function _getSubscribers(Magento_Test_Application $application)
    {
        $subscribers = parent::_getSubscribers($application);
        array_unshift($subscribers, new Magento_Test_Annotation_ApiDataFixture("{$this->_fixturesBaseDir}/api"));
        return $subscribers;
    }
}
