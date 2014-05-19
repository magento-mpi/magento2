<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for behavior tests
 */
namespace Magento\ImportExport\Model\Source\Import;

abstract class AbstractBehaviorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Model for testing
     *
     * @var \Magento\ImportExport\Model\Source\Import\AbstractBehavior
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }
}
