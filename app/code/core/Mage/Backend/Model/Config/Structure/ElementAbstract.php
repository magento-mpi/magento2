<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Backend_Model_Config_Structure_ElementAbstract
{
    protected $_data;

    public function setData(array $data)
    {
        $this->_data = $data;
    }

    public function getId()
    {
        $this->_data['id'];
    }

    public function getLabel()
    {
        return $this->_data['label'];
    }

    public function isDisplayed($default, $website, $store)
    {

    }
}
