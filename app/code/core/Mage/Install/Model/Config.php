<?php
/**
 * Install config
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Config extends Varien_Simplexml_Config
{
    public function __construct() 
    {
        parent::__construct(Mage::getConfig()->getModuleDir('etc','Mage_Install').DS.'install.xml');
    }
    
    /**
     * Get array of wizard steps
     * 
     * array(
     *      $inndex => Varien_Object
     * )
     * 
     * @return array
     */
    public function getWizardSteps()
    {
        $steps = array();
        foreach ((array)$this->getNode('wizard/steps') as $stepName=>$step) {
            $stepObject = new Varien_Object((array)$step);
            $stepObject->setName($stepName);
            $steps[] = $stepObject;
        }
        return $steps;
    }
    
    /**
     * File system check config
     * 
     * array(
     *      ['writeable'] => array(
     *          [$index] => array(
     *              ['path']
     *              ['recursive']
     *          )
     *      )
     * )
     * 
     * @return array
     */
    public function getPathForCheck()
    {
        $res = array();
        
        $items = (array) $this->getNode('check/filesystem/writeable');
        
        foreach ($items['items'] as $item) {
            $res['writeable'][] = (array) $item;
        }
        
        return $res;
    }
    
    public function getExtensionsForCheck()
    {
        $res = array();
        $items = (array) $this->getNode('check/php/extensions');
        
        foreach ($items as $name=>$value) {
            if (!empty($value)) {
                $res[$name] = array();
                foreach ($value as $subname=>$subvalue) {
                    $res[$name][] = $subname;
                }
            }
            else {
                $res[$name] = (array) $value;
            }
        }
        
        return $res;
    }
}