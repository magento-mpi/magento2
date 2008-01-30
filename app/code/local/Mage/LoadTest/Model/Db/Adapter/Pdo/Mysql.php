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
 * @category   Mage
 * @package    Mage_Loadtest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_LoadTest_Model_Db_Adapter_Pdo_Mysql extends Varien_Db_Adapter_Pdo_Mysql
{

    public function __construct($config)
    {
        $profiler = new Zend_Db_Profiler();
        $profilerId = spl_object_hash($profiler);

        if (!($profilers = Mage::registry('loadtest_db_profilers'))) {
            $profilers = new Varien_Object();
            Mage::register('loadtest_db_profilers', $profilers);
        }
        if (!$profilers->getData($profilerId)) {
            $profilers->setData($profilerId, $profiler);
        }

        $config[Zend_DB::PROFILER] = $profiler;
        parent::__construct($config);
    }

}
