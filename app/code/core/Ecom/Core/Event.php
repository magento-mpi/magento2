<?php

#Ecom::loadInterface('Ecom_Core_Event_Interface');
#include_once 'Ecom/Core/Event/Abstract.php';

/**
 * Event class
 * 
 * Ecom::addEvent(new Ecom_Event('core.modules.onload'));
 * ...
 * Ecom::getEvent('core.modules.onload')->addObserver(new Ecom_Event_Observer(array($this, 'Run')));
 * ...
 * Ecom::getEvent('core.modules.onload')->dispatch(array('arg1'=>'value1'));
 * 
 * @author Moshe Gurvich <moshe@varien.com>
 */
class Ecom_Core_Event extends Ecom_Core_Event_Abstract
{
	
}