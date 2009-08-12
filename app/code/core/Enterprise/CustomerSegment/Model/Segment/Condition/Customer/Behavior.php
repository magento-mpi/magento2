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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Behavior extends Mage_Rule_Model_Condition_Abstract
{
    
    /**
     * Return fake attributes for customer behavior
     *
     * @return array
     */
    private function _getBehaviorAttributes()
    {
      $attributes = array();  

      $attributes[] = array(
        'code' => 'account_created',
        'label' => 'Account created',
        'type' => 'date',
      );  

      $attributes[] = array(
        'code' => 'days_since_registered',
        'label' => 'Days Since Registered',
        'type' => '',
      );  

      $attributes[] = array(
        'code' => 'last_logged_in',
        'label' => 'Last Logged In',
        'type' => 'date',
      );  

      $attributes[] = array(
        'code' => 'days_since_last_logged_in',
        'label' => 'Days Since Last Logged In',
        'type' => '',
      );  

      $attributes[] = array(
        'code' => 'number_of_logins',
        'label' => 'Number of Logins',
        'type' => '',
      );  

      $attributes[] = array(
        'code' => 'days_logged_in',
        'label' => 'Days Logged In',
        'type' => '',
      );  
      
      $attributes[] = array(
        'code' => 'is_subscribed_to_newsletter',
        'label' => 'Is subscribed to newsletter',
        'type' => '',
      );  
      
      $attributes[] = array(
        'code' => 'number_of_reviews',
        'label' => 'Number of Reviews',
        'type' => '',
      );  
      
      $attributes[] = array(
        'code' => 'average_review_rating',
        'label' => 'Average Review Rating',
        'type' => '',
      );  
      
      $attributes[] = array(
        'code' => 'number_of_tags',
        'label' => 'Number of Tags',
        'type' => '',
      );  
      
      $attributes[] = array(
        'code' => 'referrals_number',
        'label' => 'Referrals number',
        'type' => '',
      );  
    
      $result = array();
      foreach ($attributes as $kay => $params)
      {
            $result[$params['code']] = $params;    
      }
      
      return $result;
    }

    /**
     * Retrieve attribute object
     *
     * @return array
     */
    public function getAttributeObject()
    {
        $attributes = $this->_getBehaviorAttributes();
        if (isset($attributes[$this->getAttribute()]))
        {
            return $attributes[$this->getAttribute()];
        }
        return false;
    }

    /**
     * Load attribute options
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Behavior
     */
    public function loadAttributeOptions()
    {
        $attributes = array();;
        foreach ($this->_getBehaviorAttributes() as $attribute)
        {
            $attributes[$attribute['code']] = $attribute['label'];    
        }  
        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getInputType()
    {
        $object = $this->getAttributeObject();
        $type = false;
        if (is_array($object) && isset($object['type'])) {
            $type = $object['type'];
        }
        if (!$type) {
            return 'string';
        }
        switch ($type) {
            case 'select': 
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'string';
        }
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        $object = $this->getAttributeObject();
        $type = false;
        if (is_array($object) && isset($object['type'])) {
            $type = $object['type'];
        }
        if (!$type) {
            return 'text';
        }
        switch ($type) {
            case 'select': 
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }

    /**
     * Retrieve value element
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_array($this->getAttributeObject())) {
            $object = $this->getAttributeObject();
            switch ($object['type']) {
                case 'date':
                    $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                    break;
            }
        }

        return $element;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        if (is_array($this->getAttributeObject())) {
            $object = $this->getAttributeObject();
            switch ($object['type']) {
                case 'date':
                    return true;
            }
        }
        return false;
    }

    /**
     * Retrieve attribute element
     *
     * @return Varien_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }
    
    
}
