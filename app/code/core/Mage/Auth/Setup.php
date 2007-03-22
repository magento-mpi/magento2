<?php

class Mage_Auth_Setup extends Mage_Core_Module_Setup
{
    function load_admin_action_preDispatch()
    {
        Mage::register('acl', new Mage_Auth_Acl());
        Mage::register('auth', new Mage_Auth_Authentication());
    }
}