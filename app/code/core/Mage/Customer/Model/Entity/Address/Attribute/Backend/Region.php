<?php
/**
 * Address region attribute backend
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Entity_Address_Attribute_Backend_Region extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $region = $object->getData('region');
        if ($regionId = (int) $region) {
            $regionModel = Mage::getModel('directory/region')->load($regionId);
            if ($regionModel->getId()) {
                if ($object->getCountryId()==$regionModel->getCountryId()) {
                    $object->setRegionId($regionModel->getId())
                        ->setRegion($regionModel->getName());
                }
                else {
                    Mage::throwException('Wrong region id by selected country');
                }
            }
        }
        return $this;
    }
}
