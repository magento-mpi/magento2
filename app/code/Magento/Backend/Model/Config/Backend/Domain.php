<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;

/**
 * Backend model for domain config value
 */
class Domain extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate a domain name value
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        $validator = new \Zend\Validator\Hostname(\Zend\Validator\Hostname::ALLOW_ALL);

        // Empty value is treated valid and will be handled when read the value out
        if (!empty($value) && !$validator->isValid($value)) {
            throw new \Magento\Framework\Model\Exception(
                'Invalid domain name: ' . join('; ', $validator->getMessages())
            );
        }
    }
}
