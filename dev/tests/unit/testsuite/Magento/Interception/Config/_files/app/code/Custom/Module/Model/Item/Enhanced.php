<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Custom_Module_Model_Item_Enhanced extends Custom_Module_Model_Item
{
    /**
     * @return string
     */
    public function getName()
    {
        return ucfirst(parent::getName());
    }
}
