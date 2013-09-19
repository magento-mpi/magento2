<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Model_Config_ReaderTest extends PHPUnit_Framework_TestCase
{

    protected $_reader;

    public function setUp()
    {


        $this->_reader = new Magento_GiftRegistry_Model_Config_Reader(
            $fileResolverMock,
            $schemaLocatorMock,
            $validationStateMock,


        );
    }
}
