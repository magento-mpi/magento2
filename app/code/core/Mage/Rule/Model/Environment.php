<?php

class Mage_Rule_Model_Environment extends Varien_Object 
{
    /**
     * Collect application environment for rules filtering
     *
     * @todo make it not dependent on checkout module
     * @return Mage_Rule_Model_Environment
     */
    public function collect()
    {
        $this->setNow(time());

        Mage::dispatchEvent('rule_environment_collect', array('env'=>$this));
        
        return $this;
    }
}