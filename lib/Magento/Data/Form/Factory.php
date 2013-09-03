<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Magento\Data\Form;

class Factory
{
    /**
     * Create Magento data form with provided params
     *
     * @param array $data
     * @return \Magento\Data\Form
     */
    public function create(array $data = array())
    {
        return new \Magento\Data\Form($data);
    }
}
