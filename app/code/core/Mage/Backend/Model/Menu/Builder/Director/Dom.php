<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Builder_Director_Dom
{
    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {

    }

    /**
     * @param Mage_Backend_Model_Menu_Builder_Simplexml $builder
     * @return Mage_Backend_Model_Menu_Builder_Director_Dom
     */
    public function command(Mage_Backend_Model_Menu_Builder_Simplexml $builder)
    {
        return $this;
    }
}
