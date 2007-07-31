<?php

class Mage_Core_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }

    /**
     * Get checksum for one or more tables
     *
     * @param string|array $tables string is separated by comma
     * @return integer|boolean
     */
    public function getChecksum($tables)
    {
        if (is_string($tables)) {
            $tablesArr = explode(',', $tables);
            $tables = array();
            foreach ($tablesArr as $table) {
                $table = $this->getTable(trim($table));
                if (!empty($table)) {
                    $tables[] = $table;
                }
            }
        }
        if (empty($tables)) {
            return false;
        }
        $checksumArr = $this->getConnection('read')
        	->fetchAll('checksum table '.join(',', $tables));
        $checksum = 0;
        foreach ($checksumArr as $r) {
            $checksum += $r['Checksum'];
        }
        return $checksum;
    }

    /**
     * Load configuration values into xml config object
     *
     * @param Mage_Core_Model_Config $xmlConfig
     * @param string $cond
     * @return Mage_Core_Model_Mysql4_Config_Collection
     */
    public function loadToXml(Mage_Core_Model_Config $xmlConfig, $cond=null)
    {
        $read = $this->getConnection('read');

        #$tables = $read->fetchAll("show tables like 'core_%'");
        #print_r($tables);

        $config = array();

        // load websites and stores from db
        $d['websites'] = $read->fetchAssoc("select website_id, code, name from ".$this->getTable('website')." where website_id>0");
        $d['stores'] = $read->fetchAssoc("select store_id, code, name, website_id from ".$this->getTable('store')." where store_id>0");
#print_r($websites);
        // initialize websites config
        foreach ($d['websites'] as $wId=>$wData) {
            $config['websites'][$wId]['system/website/id']['value'] = $wId;
            $config['websites'][$wId]['system/website/name']['value'] = $wData['name'];
        }

        //initialize stores config
        foreach ($d['stores'] as $sId=>$sData) {
            $wId = $sData['website_id'];
            $d['websites'][$wId]['stores'][$sId] = $sData['website_id'];
            $config['websites'][$wId]['system/stores/'.$d['stores'][$sId]['code']]['value'] = $sId;
            $config['stores'][$sId]['system/store/id']['value'] = $sId;
            $config['stores'][$sId]['system/store/name']['value'] = $sData['name'];
            $config['stores'][$sId]['system/website/id']['value'] = $sData['website_id'];
        }

        // get default distribution config vars
        $vars = Mage::getConfig()->getDistroServerVars();
        foreach ($vars as $k=>$v) {
            $subst_from[] = '{{'.$k.'}}';
            $subst_to[] = $v;
        }

        // load all configuration records from database
        $rows = $read->fetchAll("select * from ".$this->getMainTable().($cond ? " where ".$cond : ''));

        // organize configuration records in $config array and associate stores to websites
        foreach ($rows as $r) {
            $r['value'] = str_replace($subst_from, $subst_to, $r['value']);
            $config[$r['scope']][$r['scope_id']][$r['path']] = array('value'=>$r['value'], 'inherit'=>$r['inherit']);
        }

        // inherit global -> website -> store configuration values
        foreach ($config['default'][0] as $path=>$data) {
            foreach ($config['websites'] as $wId=>$wConfig) {
                if (!isset($wConfig[$path]) || $wConfig[$path]['inherit']==1) {
                    $config['websites'][$wId][$path]['value'] = $data['value'];
                }
                if (!empty($d['websites'][$wId]['stores'])) {
                    foreach ($d['websites'][$wId]['stores'] as $sId=>$dummy) {
                        $sConfig = $config['stores'][$sId];
                        if (!isset($sConfig[$path]) || $sConfig[$path]['inherit']==1) {
                            $config['stores'][$sId][$path]['value'] = $config['websites'][$wId][$path]['value'];
                        }
                    }
                }
            }
        }

        // save into config object
        foreach ($config as $scope=>$scopeConfig) {
            foreach ($scopeConfig as $sId=>$sConfig) {
                foreach ($sConfig as $path=>$data) {
                    // get config prefix: 'global' or 'websites/{code}' or 'stores/{code}'
                    $prefix = $scope.($sId!==0 ? '/'.$d[$scope][$sId]['code'] : '');
#echo "<pre>".print_r($prefix.'/'.$path,1)."</pre>";

                    $xmlConfig->setNode($prefix.'/'.$path, $data['value']);
                }
            }
        }

#echo "<xmp>".$xmlConfig->getNode()->asNiceXml()."</xmp>";
        return $this;
    }
}