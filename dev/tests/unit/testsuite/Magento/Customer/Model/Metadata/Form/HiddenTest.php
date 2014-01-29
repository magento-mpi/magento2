<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

class HiddenTest extends TextTest
{
    /**
     * Create an instance of the class that is being tested
     *
     * @param string|int|bool|null $value The value undergoing testing by a given test
     * @return Hidden
     */
    protected function _getClass($value)
    {
        return new Hidden(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            false,
            $this->_stringHelper
        );
    }
}
