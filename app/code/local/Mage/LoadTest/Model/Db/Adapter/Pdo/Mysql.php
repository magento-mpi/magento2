<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_Loadtest
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Mage_LoadTest_Model_Db_Adapter_Pdo_Mysql extends Varien_Db_Adapter_Pdo_Mysql
{

    public function __construct($config)
    {
        $profiler = new Mage_LoadTest_Model_Db_Profiler();
        $profiler->setEnabled(true);
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
