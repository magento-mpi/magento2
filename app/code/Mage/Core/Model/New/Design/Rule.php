<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_New_Design_Rule
{
    protected $condition;
    protected $designChange;

    public function __construct(
        Mage_Core_Model_New_Design_Rule_Condition $condition,
        Mage_Core_Model_New_Design_Change $designChange
    ) {
        $this->condition = $condition;
        $this->designChange = $designChange;
    }

    public function match()
    {
        return $this->condition->match();
    }

    public function getDesignChange()
    {
        return $this->designChange;
    }
}
