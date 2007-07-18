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
        $websites = $read->fetchAssoc("select website_id, code, name from ".$this->getTable('website'));
        $stores = $read->fetchAssoc("select store_id, code, name, website_id from ".$this->getTable('store'));
#print_r($websites);
        // initialize websites config
        foreach ($websites as $wId=>$wData) {
            $config['website'][$wId]['system/website/id']['value'] = $wId;
            $config['website'][$wId]['system/website/name']['value'] = $wData['name'];
        }
        
        //initialize stores config
        foreach ($stores as $sId=>$sData) {
            $wId = $sData['website_id'];
            $websites[$wId]['stores'][$sId] = 1;
            $config['website'][$wId]['system/stores/'.$stores[$sId]['code']]['value'] = $sId;
            $config['store'][$sId]['system/store/id']['value'] = $wId;
            $config['store'][$sId]['system/store/name']['value'] = $sData['name'];
            $config['store'][$sId]['system/website/id']['value'] = $sData['website_id'];
        }
        
        // load all configuration records from database
        $rows = $read->fetchAll("select * from ".$this->getMainTable().($cond ? " where ".$cond : ''));

        // get default distribution config vars
        $vars = Mage::getModel('core/config')->getDistroServerVars();
        foreach ($vars as $k=>$v) {
            $subst_from[] = '{{'.$k.'}}';
            $subst_to[] = $v;
        }
        // organize configuration records in $config array and associate stores to websites
        foreach ($rows as $r) {
            $r['data'] = str_replace($subst_from, $subst_to, $r['data']);
            $config[$r['scope']][$r['scope_id']][$r['path']] = array('value'=>$r['data'], 'inherit'=>$r['inherit']);
        }

        // inherit global -> website -> store configuration values
        foreach ($config['default'][0] as $path=>$data) {
            foreach ($config['website'] as $wId=>$wConfig) {
                if (!isset($wConfig[$path]) || !empty($wConfig[$path]['inherit'])) {
                    $config['website'][$wId][$path]['value'] = $data['value'];
                }
                foreach ($websites[$wId]['stores'] as $sId=>$dummy) {
                    $sConfig = $config['store'][$sId];
                    if (!isset($sConfig[$path]) || !empty($sConfig[$path]['inherit'])) {
                        $config['store'][$sId][$path]['value'] = $config['website'][$wId][$path]['value'];
                    }
                }
            }
        }
        
        // save into config object
        foreach ($config as $scope=>$scopeConfig) {
            foreach ($scopeConfig as $sId=>$sConfig) {
                foreach ($sConfig as $path=>$data) {
                    // get config prefix: 'global' or 'websites/{code}' or 'stores/{code}'
                    $prefix = $scope.($sId!==0 ? 's/'.${$scope.'s'}[$sId]['code'] : '');
                    $xmlConfig->setNode($prefix.'/'.$path, $data['value']);
                }
            }
        }
#echo "<xmp>".$xmlConfig->getNode()->asNiceXml()."</xmp>";
        return $this;
    }
    
    public function loadWithDefaults($section, $websiteCode, $storeCode)
    {
        $read = $this->getConnection('read');
        $table = $this->getMainTable();
        
        $config = array();
        
        /**
         * read default config into 
         * path=>array(
         *   default_value
         * )
         */
        $defaultConfig = $read->fetchAssoc(
            $read->select()->from($table, array('path', 'data'))
                ->where("scope='default'")
                ->where("path like ?", $section.'/%')
        );
        foreach ($defaultConfig as $path=>$data) {
            $config[$path] = array(
                'value'=>$data['data'], 
                'default_value'=>$data['data'],
                'inherit'=>1,
            );
        }
        
        /**
         * read website config into 
         * path=>array(
         *   value, 
         *   inherit, - if website config requested
         *   default_value - if store config requested
         * )
         */
        if ($websiteCode) {
            $websiteId = (int)Mage::getConfig()->getNode("websites/$websiteCode/system/website/id");
            $websiteConfig = $read->fetchAssoc(
                $read->select()->from($table, array('path', 'data', 'inherit'))
                    ->where("scope='website' and scope_id=?", $websiteId)
                    ->where("path like ?", $section.'/%')
            );
            foreach ($websiteConfig as $path=>$data) {
                $config[$path]['value'] = $data['data'];
                if ($storeCode) {
                    $config[$path]['default_value'] = $data['data'];
                } else {
                    $config[$path]['inherit'] = $data['inherit'];
                }
            }
        }
        
        /**
         * read store config into
         * path=>array(
         *   value,
         *   inherit
         * )
         */
        if ($storeCode) {
            $storeId = (int)Mage::getConfig()->getNode("stores/$storeCode/system/store/id");
            $storeConfig = $read->fetchAssoc(
                $read->select()->from($table, array('path', 'data', 'inherit'))
                    ->where("scope='store' and scope_id=?", $storeId)
                    ->where("path like ?", $section.'/%')
            );
            foreach ($storeConfig as $path=>$data) {
                $config[$path]['value'] = $data['data'];
                $config[$path]['inherit'] = $data['inherit'];
            }
        }
        
        return $config;
    }

}