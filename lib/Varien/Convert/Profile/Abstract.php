<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert profile
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author     Moshe Gurvich <moshe@varien.com>
 */
abstract class Varien_Convert_Profile_Abstract extends Varien_Convert_Component_Abstract
{
    protected $_components;
    
    public function getComponents()
    {
        if (!$this->_components) {
            $this->_components = new Varien_Convert_Component_Collection();
            $this->_components->setDefaultClass('Varien_Convert_Container_Generic');
        }
        return $this->_components;
    }
    
    public function addAction($action, array $params=array(), array $vars=array())
    {
        $action = $this->getComponents()->addComponent(null, $action, $params, $vars);
        $action->setProfile($this);

        return $this;
    }
    
    public function getContainer($name)
    {
        if (!isset($this->_containers[$name])) {
            $container = new Varien_Convert_Container_Generic();
            $this->setContainer($name, $container);
        }
        return $this->_containers[$name];
    }
    
    public function getDefaultContainer()
    {
        return $this->getContainer('default');
    }
    
    public function addContainer($container, array $params=array(), array $vars=array())
    {
        $container = $this->getComponents()->addComponent(null, $container, $params, $vars);
        $container->setProfile($this);
        return $this;
    }
    
    public function run()
    {
        foreach ($this->getComponents() as $action) {
            $action->run();
        }   
        return $this;
    }
}