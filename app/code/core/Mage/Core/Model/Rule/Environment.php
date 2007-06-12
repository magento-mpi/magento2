<?php

class Mage_Core_Model_Rule_Environment extends Varien_Object 
{
    /**
     * Collect application environment for rules filtering
     *
     * @todo make it not dependent on checkout module
     * @return Mage_Core_Model_Rule_Environment
     */
    public function collect()
    {
        $this->setNow(time());

        Mage::dispatchEvent('core_rule_environment_collect', array('env'=>$this));
        
        return $this;
    }
}