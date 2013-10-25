<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

/**
 * Encrypt filter
 */
class Encrypt extends \Zend_Filter_Encrypt
{
    /**
     * @param \Magento\Filter\Encrypt\AdapterInterface $adapter
     */
    public function __construct(\Magento\Filter\Encrypt\AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }
}
