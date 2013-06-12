<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_New_Design_Detection
{
    protected $design;

    public function __construct(Mage_Core_Model_New_Design $design) {
        $this->design = $design;
    }

    public function detectDesign()
    {
        /** @var $rule Mage_Core_Model_New_Design_Rule */
        foreach ($this->getDesignRules() as $rule) {
            if ($rule->match()) {
                $this->design->applyChange($rule->getDesignChange());
            }
        }
    }

    public function getDesignRules()
    {
        //@TODO Use some kind of Builder and load rules from DB or cache
        return array(
            new Mage_Core_Model_New_Design_Rule(new Mage_Core_Model_New_Design_Rule_Condition, new Mage_Core_Model_New_Design_Change),
            new Mage_Core_Model_New_Design_Rule(new Mage_Core_Model_New_Design_Rule_Condition, new Mage_Core_Model_New_Design_Change),
        );
    }
}
