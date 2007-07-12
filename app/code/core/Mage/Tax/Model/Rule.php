<?php
class Mage_Tax_Model_Rule extends Varien_Object
{
    public function getResource()
    {
        return Mage::getResourceModel('tax/rule');
    }

    public function load($ruleId)
    {
        return $this->getResource()->load($ruleId);
    }

    public function save($ruleObject)
    {
        return $this->getResource()->save($ruleObject);
    }

    public function delete($ruleObject)
    {
        return $this->getResource()->delete($ruleObject);
    }
}