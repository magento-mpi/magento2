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
class Magento_Test_Bootstrap_DocBlock
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
    public function registerAnnotations(Magento_Test_Application $application)
    {
        $eventManager = new Magento_Test_EventManager($this->_getSubscribers($application));
        Magento_Test_Event_PhpUnit::setDefaultEventManager($eventManager);
        Magento_Test_Event_Magento::setDefaultEventManager($eventManager);
    }

    /**
     * Get list of subscribers.
     *
     * Note: order of registering (and applying) annotations is important.
     * To allow config fixtures to deal with fixture stores, data fixtures should be processed first.
     *
     * @param Magento_Test_Application $application
     * @return array
     */
    protected function _getSubscribers(Magento_Test_Application $application)
    {
        return array(
            new Magento_Test_Workaround_Segfault(),
            new Magento_Test_Workaround_Cleanup_TestCaseProperties(),
            new Magento_Test_Workaround_Cleanup_StaticProperties(),
            new Magento_Test_Isolation_WorkingDirectory(),
            new Magento_Test_Annotation_AppIsolation($application),
            new Magento_Test_Event_Transaction(new Magento_Test_EventManager(array(
                new Magento_Test_Annotation_DbIsolation(),
                new Magento_Test_Annotation_DataFixture($this->_fixturesBaseDir)
            ))),
            new Magento_Test_Annotation_ConfigFixture(),
        );
    }
}
