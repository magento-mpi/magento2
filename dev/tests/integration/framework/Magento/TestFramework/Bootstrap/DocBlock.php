<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap of the custom DocBlock annotations
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_TestFramework_Bootstrap_DocBlock
{
    /**
     * @var string
     */
    protected $_fixturesBaseDir;

    /**
     * @param string $fixturesBaseDir
     */
    public function __construct($fixturesBaseDir)
    {
        $this->_fixturesBaseDir = $fixturesBaseDir;
    }

    /**
     * Activate custom DocBlock annotations along with more-or-less permanent workarounds
     */
    public function registerAnnotations(Magento_TestFramework_Application $application)
    {
        $eventManager = new Magento_TestFramework_EventManager($this->_getSubscribers($application));
        Magento_TestFramework_Event_PhpUnit::setDefaultEventManager($eventManager);
        Magento_TestFramework_Event_Magento::setDefaultEventManager($eventManager);
    }

    /**
     * Get list of subscribers.
     *
     * Note: order of registering (and applying) annotations is important.
     * To allow config fixtures to deal with fixture stores, data fixtures should be processed first.
     *
     * @param Magento_TestFramework_Application $application
     * @return array
     */
    protected function _getSubscribers(Magento_TestFramework_Application $application)
    {
        return array(
            new Magento_TestFramework_Workaround_Segfault(),
            new Magento_TestFramework_Workaround_Cleanup_TestCaseProperties(),
            new Magento_TestFramework_Workaround_Cleanup_StaticProperties(),
            new Magento_TestFramework_Isolation_WorkingDirectory(),
            new Magento_TestFramework_Annotation_AppIsolation($application),
            new Magento_TestFramework_Event_Transaction(new Magento_TestFramework_EventManager(array(
                new Magento_TestFramework_Annotation_DbIsolation(),
                new Magento_TestFramework_Annotation_DataFixture($this->_fixturesBaseDir)
            ))),
            new Magento_TestFramework_Annotation_AppArea($application),
            new Magento_TestFramework_Annotation_ConfigFixture(),
        );
    }
}
