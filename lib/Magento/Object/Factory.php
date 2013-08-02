<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Object
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Magento_Object_Factory
{
    /**
     * Create Magento object with provided params
     *
     * @param array $data
     * @return Magento_Object
     */
    public function create(array $data = array())
    {
        return new Magento_Object($data);
    }
}
