<?php
class Mage_Tax_Model_Rule extends Varien_Object
{
    public function __construct($rule=false)
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function getResource()
    {
        return Mage::getResourceModel('tax/rule');
    }

    public function load($ruleId)
    {
        $this->getResource()->load($this, $ruleId);
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }

    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
}