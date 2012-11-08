<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Object
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Varien_Object_Factory
{
    /**
     * Create varien object with provided params
     *
     * @param array $data
     * @return Varien_Object
     */
    public function create(array $data = array())
    {
        return new Varien_Object($data);
    }
}
