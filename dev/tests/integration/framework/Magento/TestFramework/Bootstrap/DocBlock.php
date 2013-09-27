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
namespace Magento\TestFramework\Bootstrap;

class DocBlock
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
    public function registerAnnotations(\Magento\TestFramework\Application $application)
    {
        $eventManager = new \Magento\TestFramework\EventManager($this->_getSubscribers($application));
        \Magento\TestFramework\Event\PhpUnit::setDefaultEventManager($eventManager);
        \Magento\TestFramework\Event\Magento::setDefaultEventManager($eventManager);
    }

    /**
     * Get list of subscribers.
     *
     * Note: order of registering (and applying) annotations is important.
     * To allow config fixtures to deal with fixture stores, data fixtures should be processed first.
     *
     * @param \Magento\TestFramework\Application $application
     * @return array
     */
    protected function _getSubscribers(\Magento\TestFramework\Application $application)
    {
        return array(
            new \Magento\TestFramework\Workaround\Segfault(),
            new \Magento\TestFramework\Workaround\Cleanup\TestCaseProperties(),
            new \Magento\TestFramework\Workaround\Cleanup\StaticProperties(),
            new \Magento\TestFramework\Isolation\WorkingDirectory(),
            new \Magento\TestFramework\Annotation\AppIsolation($application),
            new \Magento\TestFramework\Event\Transaction(new \Magento\TestFramework\EventManager(array(
                new \Magento\TestFramework\Annotation\DbIsolation(),
                new \Magento\TestFramework\Annotation\DataFixture($this->_fixturesBaseDir)
            ))),
            new \Magento\TestFramework\Annotation\AppArea($application),
            new \Magento\TestFramework\Annotation\ConfigFixture(),
        );
    }
}
