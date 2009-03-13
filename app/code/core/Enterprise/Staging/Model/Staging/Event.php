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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Enterprise_Staging_Model_Staging_Event extends Mage_Core_Model_Abstract
{
    /**
     * Staging instance
     *
     * @var Enterprise_Staging_Model_Staging
     */
    protected $_staging;

    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_event');
    }

    /**
     * Declare staging instance
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_Event_History
     */
    public function setStaging(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_staging = $staging;
        return $this;
    }

    /**
     * Retrieve staging instance
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        return $this->_staging;
    }

    /**
     * Retrieve staging status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return Enterprise_Staging_Model_Staging_Config::getStatusLabel($this->getStatus());
    }

    /**
     * Retrieve staging event label
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return Enterprise_Staging_Model_Staging_Config::getEventLabel($this->getCode());
    }
}