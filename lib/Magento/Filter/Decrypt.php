<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

/**
 * Decrypt filter
 */
class Decrypt extends \Zend_Filter_Decrypt
{
    /**
     * @param \Magento\Filter\Encrypt\AdapterInterface $adapter
     */
    public function __construct(\Magento\Filter\Encrypt\AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }
}
