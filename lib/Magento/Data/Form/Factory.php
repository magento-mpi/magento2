<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Magento_Data_Form_Factory
{
    /**
     * Create Magento data form with provided params
     *
     * @param array $data
     * @return Magento_Data_Form
     */
    public function create(array $data = array())
    {
        return new Magento_Data_Form($data);
    }
}
