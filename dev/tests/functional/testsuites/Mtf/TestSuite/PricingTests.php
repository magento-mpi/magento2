<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

use Mtf\ObjectManagerFactory;
use Mtf\ObjectManager;
use Mtf\TestRunner\Configuration;

/**
 * Class PricingTests
 *
 * @package Mtf\TestSuite
 */
class PricingTests extends InjectableTests
{
    /**
     * Prepare test suite and apply application state
     *
     * @return \Mtf\TestSuite\AppState
     */
    public function prepareSuite()
    {
        $this->init();
        return $this->objectManager->create('Mtf\TestSuite\TestCase');
    }
}
