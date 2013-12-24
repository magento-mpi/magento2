<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote;

/**
 * Wrapper class to expose the protected methods for testing
 */
class AddressFixture extends Address
{
    /**
     * Expose the protected method _beforeSave()
     */
    public function populateBeforeSaveData()
    {
        $this->_populateBeforeSaveData();
    }
}
