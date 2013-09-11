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
abstract class Magento_ImportExport_Model_Source_Import_BehaviorTestCaseAbstract extends PHPUnit_Framework_TestCase
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
