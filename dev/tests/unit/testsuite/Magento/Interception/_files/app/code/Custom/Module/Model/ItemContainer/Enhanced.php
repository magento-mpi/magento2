<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Custom_Module_Model_ItemContainer_Enhanced extends Custom_Module_Model_ItemContainer
{
    /**
     * @return string
     */
    public function getName()
    {
        return parent::getName() . '_enhanced';
    }
}
