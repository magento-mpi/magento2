<?php
/**
 * Json controller
 *
 * @package    Mage
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_JsonController extends Mage_Core_Controller_Front_Action 
{
    public function childRegionAction()
    {
        $arrRes = array();
            
        $countryId = $this->getRequest()->getParam('parent');
        $arrRegions = Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId)
            ->load()
            ->toOptionArray();
        
        if (!empty($arrRegions)) {
            foreach ($arrRegions as $region) {
                $arrRes[] = $region;
            }
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($arrRes));
    }
}