<?php
class Saas_Payment_Model_Method_Ccsave extends Mage_Payment_Model_Method_Ccsave
{
    /**
     * Forced payment disabling
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return false;
    }
} 