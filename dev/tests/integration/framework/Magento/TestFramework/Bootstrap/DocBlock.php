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
 */
namespace Magento\TestFramework\Bootstrap;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DocBlock
{
    /**
     * @var string
     */
    private $_fixturesBaseDir;

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
        /*
         * Note: order of registering (and applying) annotations is important.
         * To allow config fixtures to deal with fixture stores, data fixtures should be processed first.
         */
        $eventManager = new \Magento\TestFramework\EventManager(array(
            new \Magento\TestFramework\Workaround\Segfault(),
            new \Magento\TestFramework\Workaround\Cleanup\TestCaseProperties(),
            new \Magento\TestFramework\Workaround\Cleanup\StaticProperties(),
            new \Magento\TestFramework\Isolation\WorkingDirectory(),
            new \Magento\TestFramework\Annotation\AppIsolation($application),
            new \Magento\TestFramework\Event\Transaction(new \Magento\TestFramework\EventManager(array(
                new \Magento\TestFramework\Annotation\DbIsolation(),
                new \Magento\TestFramework\Annotation\DataFixture($this->_fixturesBaseDir),
            ))),
            new \Magento\TestFramework\Annotation\AppArea($application),
            new \Magento\TestFramework\Annotation\ConfigFixture(),
        ));
        \Magento\TestFramework\Event\PhpUnit::setDefaultEventManager($eventManager);
        \Magento\TestFramework\Event\Magento::setDefaultEventManager($eventManager);
    }
}
