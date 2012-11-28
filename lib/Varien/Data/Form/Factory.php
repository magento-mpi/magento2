<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Varien_Data_Form_Factory
{
    /**
     * Create varien data form with provided params
     *
     * @param array $data
     * @return Varien_Data_Form
     */
    public function create(array $data = array())
    {
        return new Varien_Data_Form($data);
    }
}
