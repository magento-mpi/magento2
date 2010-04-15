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
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Pool flag model
 *
 */
class Enterprise_SalesPool_Model_Flag extends Enterprise_Enterprise_Model_Core_Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'enterprise_salespool';

    /**
     * Retrieve last flush time
     *
     * @return int
     */
    public function getLastFlushTime()
    {
        $data = (array) $this->getFlagData();
        return (isset($data['last_flush_time']) ? $data['last_flush_time'] : 0);
    }

    /**
     * Set last flush time
     *
     * @return Enterprise_SalesPool_Model_Flag
     */
    public function setLastFlushTime()
    {
        $data = (array) $this->getFlagData();
        $data['last_flush_time'] = time();
        $this->setFlagData($data);
        return $this;
    }
}
