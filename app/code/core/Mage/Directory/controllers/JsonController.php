<?php
/**
 * Json controller
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_JsonController extends Mage_Core_Controller_Front_Action 
{
    protected function _construct()
    {
        $this->setFlag('', 'no-preDispatch', true);
        $this->setFlag('', 'no-defaultLayout', true);
        $this->setFlag('', 'no-postDispatch', true);
    }
    
    public function childRegionAction()
    {
        $arrRes = array();
            
        $countryId = $this->getRequest()->getParam('parent');
        $arrRegions = Mage::getModel('directory', 'region_collection')
            ->addCountryFilter($countryId)
            ->load()
            ->getItems();
        
        if (!empty($arrRegions)) {
            foreach ($arrRegions as $region) {
                $arrRes[] = array(
                    'value' => $region->getRegionId(),
                    'label' => $region->getName(),
                    'index' => $region->getCode()
                );
            }
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($arrRes));
    }
}