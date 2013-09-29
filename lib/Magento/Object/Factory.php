<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    \Magento\Object
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Magento\Object;

class Factory
{
    /**
     * Create Magento object with provided params
     *
     * @param array $data
     * @return \Magento\Object
     */
    public function create(array $data = array())
    {
        return new \Magento\Object($data);
    }
}
