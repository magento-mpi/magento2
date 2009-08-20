<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Custom handlers for models logging
 *
 */
class Enterprise_Logging_Model_Handler_Models
{

    /**
     * Collection of affected ids
     *
     * @var array
     */
    protected $_collectedIds = array();

    /**
     * Set of fields that should not be logged
     *
     * @var array
     */
    protected $_skipFields = array();

    const XML_PATH_SKIP_FIELDS = 'adminhtml/enterprise/logging/skip_fields';

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        $this->_skipFields = array_map('trim', array_filter(explode(',',
            (string)Mage::getConfig()->getNode(self::XML_PATH_SKIP_FIELDS))));
    }

    /**
     * SaveAfter handler
     *
     * @param object Mage_Core_Model_Abstract $model
     * @return object Enterprise_Logging_Event_Changes or false if model wasn't modified
     */
    public function modelSaveAfter($model)
    {
        $data = $this->_clearupData($model->getData());
        $origData = $this->_clearupData($model->getOrigData());
        $isDiff = false;
        foreach ($data as $key=>$value){
            switch (true){
                case (isset($origData[$key]) && $value == $origData[$key]):
                    unset($data[$key]);
                    unset($origData[$key]);
                    break;
                case (isset($origData[$key]) && $value != $origData[$key]):
                case (!isset($origData[$key])):
                default:
                    $isDiff = true;
                    break;
            }
        }
        if ($isDiff){
            $this->_collectedIds[get_class($model)][] = $model->getId();
            return Mage::getModel('enterprise_logging/event_changes')->setData(
                array(
                    'original_data' => $origData,
                    'result_data'   => $data,
                ));
        } else {
            return false;
        }
    }

    /**
     * Delete after handler
     *
     * @param object Mage_Core_Model_Abstract $model
     * @return object Enterprise_Logging_Event_Changes
     */
    public function modelDeleteAfter($model)
    {
        $this->_collectedIds[get_class($model)][] = $model->getId();
        $origData = $this->_clearupData($model->getOrigData());
        return Mage::getModel('enterprise_logging/event_changes')
                    ->setData(array('original_data'=>$origData, 'result_data'=>null));
    }

    /**
     * MassUpdate after handler
     *
     * @param object Mage_Core_Model_Abstract $model
     * @return object Enterprise_Logging_Event_Changes
     */
    public function modelMassUpdateAfter($model)
    {
        return $this->modelSaveAfter($model);
    }

    /**
     * Clear model data from objects, arrays and fields that should be skipped
     *
     * @param array $data
     * @return array
     */
    protected function _clearupData($data)
    {
        if (!$data && !is_array($data)) {
            return array();
        }
        $clearData = array();
        foreach ($data as $key=>$value) {
            if (!in_array($key, $this->_skipFields) && !is_array($value) && !is_object($value)) {
                $clearData[$key] = $value;
            }
        }
        return $clearData;
    }

    /**
     * Getter for $_colectedIds value
     * It collects unique ids for each object
     *
     * @return array
     */
    public function getCollectedIds()
    {
        $ids = array();
        foreach ($this->_collectedIds as $className => $classIds) {
            $uniqueIds  = array_unique($classIds);
            $ids        = array_merge($ids, $uniqueIds);
            $this->_collectedIds[$className] = $uniqueIds;
        }
        return $ids;
    }

    /*Special modelSaveAfter handlers */

    /**
     * Special handler for Invitation module
     *
     * @param Interprise_Invitation_Model_Invitation $model
     * @return unknown
     */
    public function modelSaveAfterInvitation($model)
    {
        $this->_collectedIds[get_class($model)][] = $model->getId();
        $data = $this->_clearupData($model->getData());
        return Mage::getModel('enterprise_logging/event_changes')
            ->setData(array('original_data' => array(), 'result_data' => $data));
    }

    /**
     * Load after handler
     *
     * @param object Mage_Core_Model_Abstract $model
     */
    public function modelViewAfter($model)
    {
        $this->_collectedIds[get_class($model)][] = $model->getId();
        return Mage::getModel('enterprise_logging/event_changes')
            ->setData(array('original_data' => array(), 'result_data' => array()));
    }
}
