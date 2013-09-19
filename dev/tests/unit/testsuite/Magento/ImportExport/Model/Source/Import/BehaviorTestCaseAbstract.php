<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for behavior tests
 */
namespace Magento\ImportExport\Model\Source\Import;

abstract class BehaviorTestCaseAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * Model for testing
     *
     * @var \Magento\ImportExport\Model\Source\Import\BehaviorAbstract
     */
    protected $_model;

    public function tearDown()
    {
        unset($this->_model);
    }
}
