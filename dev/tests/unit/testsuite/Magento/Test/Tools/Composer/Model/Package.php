<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer\Model;

/**
 * Class Package
 * @package Magento\Test\Tools\Composer\Model
 */
class Package extends \PHPUnit_Framework_TestCase
{


    public function testAddDependencies(){
        $package = new \Magento\Tools\Composer\Model\Package('magento/module', '0.1.0', BP, 'module');
        $moduleOne = new \Magento\Tools\Composer\Model\Package('magento/module1', '1.2.3', BP, 'theme');
        $package->addDependencies(array($moduleOne));
        $this->assertEquals(1, sizeof($package->getDependencies()));
        foreach ($package->getDependencies() as $dependency) {
            $this->assertEquals('magento/module1', $dependency->getName());
            $this->assertEquals('1.2.3', $dependency->getVersion());
            $this->assertEquals('theme', $dependency->getType());
            $this->assertEquals(BP, $dependency->getLocation());
        }

        $moduleTwo = new \Magento\Tools\Composer\Model\Package('magento/module2', '3.4.5', BP, 'framework');
        $package->addDependencies($moduleTwo);
        $this->assertEquals(2, sizeof($package->getDependencies()));
        foreach ($package->getDependencies() as $dependency) {
            switch ($dependency->getType()) {
                case 'theme':
                    $this->assertEquals('magento/module1', $dependency->getName());
                    $this->assertEquals('1.2.3', $dependency->getVersion());
                    $this->assertEquals(BP, $dependency->getLocation());
                    break;
                case 'module':
                    $this->assertEquals('magento/module2', $dependency->getName());
                    $this->assertEquals('3.4.5', $dependency->getVersion());
                    $this->assertEquals(BP, $dependency->getLocation());
            }

        }
    }

    public function testConstructor(){
        $package = new \Magento\Tools\Composer\Model\Package('magento/module', '0.1.0', BP, 'module');

        $this->assertEquals('magento/module', $package->getName());
        $this->assertEquals('0.1.0', $package->getVersion());
        $this->assertEquals('module', $package->getType());
        $this->assertEquals(BP, $package->getLocation());
    }

}