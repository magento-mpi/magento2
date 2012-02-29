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
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 EAV Validator
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Resource_Validator_Eav extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Validate entity.
     * If fails validation, then this metod return an array of errors
     * that explain why the validation failed.
     *
     * @param array $data
     * @return array|bool
     */
    protected function _validate(array $data)
    {
        /** @var $form Mage_Eav_Model_Form */
        $form = Mage::getModel($this->_getFormPath());
        $form->setEntity($this->_getEntity())
            ->setFormCode($this->_getFormCode())
            ->ignoreInvisible(false);

        return $form->validateData($data);
    }

    /**
     * Validate entity.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param  array $data
     * @return bool
     */
    public function isSatisfiedByData(array $data)
    {
        $errors = $this->_validate($data);
        if (true !== $errors) {
            $this->_setErrors($errors);
            return false;
        }
        return true;
    }

    /**
     * Retrieve form path, which will used to initiate form validation model
     *
     * @abstract
     * @return string
     */
    abstract protected function _getFormPath();

    /**
     * Retrieve form code, which will use for attributes validation
     *
     * @abstract
     * @return string
     */
    abstract protected function _getFormCode();

    /**
     * Retrieve entity, which will be validated
     *
     * @abstract
     * @return Mage_Core_Model_Abstract
     */
    abstract protected function _getEntity();
}
