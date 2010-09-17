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
 * @package     Performance_Report
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Performance_Report_Helper
{
    /**
     * Print HTML Header
     *
     * @param string $pageTitle
     */
    public function printHeaderHtml($pageTitle = null)
    {
        $baseUrl = $_SERVER['PHP_SELF'];
        if (empty($pageTitle)) {
            $pageTitle  = 'Magento Performance Tests Report';
        }
        echo <<<HTML
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{$pageTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="*" />
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
</head>
<body>
    <div class="wrapper">
        <div class="page">
            <div class="header-container">
                <div class="header">
                    <a href="{$baseUrl}" title="Magento Commerce" class="logo"><img src="images/logo.gif" alt="Magento Commerce" /></a>
                </div>
            </div>
            <div class="main-container">
                <div class="main col1-layout">
                <!-- [start] center -->
                    <div id="main" class="col-main">
                        <!-- [start] content -->
                        <div class="page-title">
                        <h1>{$pageTitle}</h1>
                    </div>
HTML;
    }

    /**
     * Print HTML Footer
     *
     */
    public function printFooterHtml()
    {
        echo <<<HTML
                        <!-- [end] content -->
                    </div>
                <!-- [end] center -->
                </div>
            </div>
            <div class="footer-container">
                <div class="footer">
                    <address class="copyright">Magento is a trademark of Irubin Consulting Inc. DBA Varien. Copyright &copy; 2010 Irubin Consulting Inc.</address>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    public function printContentMessage($message)
    {
        echo <<<HTML
                <p>{$message}</p>
HTML;
    }

    public function printListOfReports($reports)
    {
        echo <<<HTML
<script type="text/javascript">
function validateForm(form)
{
    var cnt = 0;
    for (var i=0; i<form.elements.length; i++) {
        var el = form.elements[i];
        if (el.tagName == 'INPUT' && el.type == 'checkbox' && el.checked) {
            cnt ++;
        }
    }
    if (cnt < 2) {
        alert("Please select two or more reports for compare");
        return false;
    }
    return true;
}
</script>
<form action="{$_SERVER['PHP_SELF']}?act=compare" method="post" onsubmit="return validateForm(this)">
    <fieldset class="fieldset">
    <ul class="form-list group-select">
HTML;
        foreach ($reports as $k => $v) {
            echo <<<HTML
        <li class="fields"><input type="checkbox" name="reports[]" value="{$k}" /> &nbsp; <a href="{$_SERVER['PHP_SELF']}?act=view&r={$k}">{$v}</a></li>
HTML;
        }
        echo <<<HTML
    </ul>
    </fieldset>
    <div class="buttons-set form-buttons btn-only">
        <button name="submit" class="button" type="submit"><span><span>Compare reports</span></span></button>
    </div>
</form>
HTML;
    }

    public function printLog(Performance_Report_Log $log)
    {
        $logData  = $log->getLogData();
        $srvInfo  = $log->getServerInfo();
        $mageInfo = $log->getMagentoInfo();

        echo "<h3>Tested Server and Magento</h3>";

        echo "<dl>";
        echo "<dd>Server name:</dd>";
        echo "<dt>{$srvInfo['name']}</dt>";
        echo "<dd>Magento version:</dd>";
        echo "<dt>{$mageInfo['version']}</dt>";
        if (!empty($mageInfo['config'])) {
            echo "<dd>Magento configuration:</dd>";
            echo "<dt>{$mageInfo['config']}</dt>";
        }
        echo "</dl>";

        echo "<br /><h3>Test Results</h3>";

        echo '<script type="text/javascript" src="http://extra.amcharts.com/public/swfobject.js"></script>';

        $amSeriesXml = '<series>';
        foreach ($log->getConcurrencies() as $cId => $cName) {
            $amSeriesXml .= '<value xid=\''.$cId.'\'>'.$cName.'</value>';
        }
        $amSeriesXml .='</series>';

        $amGraphsXml = '<graphs>';
        foreach ($log->getTestTypes() as $typeId => $typeName) {
            $amGraphsXml .= '<graph gid=\''.$typeId.'\'><title>'.$typeName.'</title><line_width>2</line_width></graph>';
        }
        $amGraphsXml .= '</graphs>';

        foreach ($log->getLogColumns() as $colId => $colName) {
            $amChartId  = $colId;
            $amTitle    = $colName;
            $amDataXml  = '<chart>' . $amSeriesXml;
            $amDataXml .= '<graphs>';

            foreach ($log->getTestTypes() as $typeId => $typeName) {
                $amDataXml .= '<graph gid=\''.$typeId.'\'>';
                foreach ($log->getConcurrencies() as $cId => $cName) {
                    $val = '';
                    if (isset($logData[$typeId][$cId][$colId])) {
                        $val = $logData[$typeId][$cId][$colId];
                    }
                    $amDataXml .= '<value xid=\''.$cId.'\'>'.$val.'</value>';
                }
                $amDataXml .= '</graph>';
            }

            $amDataXml .= '</graphs>';

            echo <<<HTML
<div id="amcharts_{$amChartId}">You need to upgrade your Flash Player</div>
<script type="text/javascript">
    var so = new SWFObject("http://extra.amcharts.com/public/amline.swf", "amline", "900", "300", "8", "#FFFFFF");
    so.addVariable("path", "amline/");
    so.addVariable("chart_settings", encodeURIComponent("<settings><colors>EC7600,1F7ED8</colors><font>Tahoma</font><hide_bullets_count>18</hide_bullets_count><decimals_separator>.</decimals_separator><background><alpha>90</alpha><border_alpha>10</border_alpha></background><plot_area><margins><left>50</left><right>40</right><bottom>65</bottom></margins></plot_area><grid><x><alpha>10</alpha><approx_count>9</approx_count></x><y_left><alpha>10</alpha></y_left></grid><axes><x><width>1</width><color>0D8ECF</color></x><y_left><width>1</width><color>0D8ECF</color></y_left></axes><indicator><color>0D8ECF</color><line_alpha>50</line_alpha><selection_color>0D8ECF</selection_color><selection_alpha>20</selection_alpha></indicator><labels><label lid='0'><text><![CDATA[<b>{$amTitle}</b>]]></text><y>25</y><text_size>13</text_size><align>center</align></label></labels>{$amGraphsXml}</settings>"));
    so.addVariable("chart_data", encodeURIComponent("{$amDataXml}"));
    so.write("amcharts_{$amChartId}");
</script>
HTML;
        }

        $checkout = $log->getCheckoutData();
        if ($checkout) {
            echo "<br /><h3>Checkout Results (10 minutes)</h3>";

            $amSeriesXml = '<series>';
            $amGraphsXml = "<graphs><graph gid='0'>";

            $i = 0;
            foreach ($checkout as $cName => $orders) {
                $amSeriesXml .= "<value xid='{$i}'>{$cName}</value>";
                $amGraphsXml .= "<value xid='{$i}'>{$orders}</value>";

                $i ++;
            }

            $amSeriesXml .= '</series>';
            $amGraphsXml .= '</graph></graphs>';
            $amDataXml    = '<chart>' . $amSeriesXml . $amGraphsXml . '</chart>';

            echo <<<HTML
            <div id="amcharts_checkout">You need to upgrade your Flash Player</div>
<script type="text/javascript">
    var so = new SWFObject("http://extra.amcharts.com/public/amcolumn.swf", "amcolumn", "900", "300", "8", "#FFFFFF");
    so.addVariable("path", "amcolumn/");
    so.addVariable("chart_settings", encodeURIComponent("<settings><background><alpha>100</alpha><border_alpha>20</border_alpha></background><plot_area><margins><top>30</top><bottom>40</bottom></margins></plot_area><grid><category><dashed>1</dashed></category><value><dashed>1</dashed></value></grid><axes><category><width>1</width><color>E7E7E7</color></category><value><width>1</width><color>E7E7E7</color></value></axes><values><value><min>0</min></value></values><legend><enabled>0</enabled></legend><depth>15</depth><column><width>85</width><balloon_text>{value} order(s)</balloon_text><grow_time>3</grow_time><grow_effect>regular</grow_effect></column><graphs><graph gid='0'><color>F73C42</color></graph></graphs><labels><label lid='0'><text_color>000000</text_color><text_size>13</text_size><align>center</align></label></labels></settings>"));
    so.addVariable("chart_data", encodeURIComponent("{$amDataXml}"));
    so.write("amcharts_checkout");
</script>
HTML;
        }

        if ($srvInfo['apache']) {
            echo "<p class=\"conf-title\">Apache Configuration</p>";
            echo "<div class=\"conf-container\"><div class=\"conf\"><pre>";
            echo $srvInfo['apache'];
            echo "</pre></div></div>";
        }

        if ($srvInfo['nginx']) {
            echo "<p class=\"conf-title\">Nginx Configuration</p>";
            echo "<div class=\"conf-container\"><div class=\"conf\"><pre>";
            echo $srvInfo['nginx'];
            echo "</pre></div></div>";
        }

        if ($srvInfo['mysql']) {
            echo "<p class=\"conf-title\">MySQL Configuration</p>";
            echo "<div class=\"conf-container\"><div class=\"conf\"><pre>";
            echo $srvInfo['mysql'];
            echo "</pre></div></div>";
        }

        if ($srvInfo['php_fpm']) {
            echo "<p class=\"conf-title\">PHP-FPM Configuration</p>";
            echo "<div class=\"conf-container\"><div class=\"conf\"><pre>";
            echo htmlspecialchars($srvInfo['php_fpm']);
            echo "</pre></div></div>";
        }

        if ($srvInfo['php']) {
            echo "<p class=\"conf-title\">PHP Configuration</p>";
            echo "<div class=\"conf-container\"><div class=\"conf\"><pre>";
            echo $srvInfo['php'];
            echo "</pre></div></div>";
        }

        $data = $log->getLogData();
    }

    /**
     * Print compare logs
     *
     * @param array $reports the report id and log object pairs
     */
    public function printCompare(array $reports)
    {
        $logColumns     = null;
        $concurrencies  = array();
        $concKeys       = array();
        $testTypes      = array();
        $testKeys       = array();
        $checkout       = array();
        foreach ($reports as $rId => $log) {
            /* @var $log Performance_Report_Log */
            if (!$logColumns) {
                $logColumns = $log->getLogColumns();
            }
            $concKeys[$rId] = array();
            $testKeys[$rId] = array();
            foreach ($log->getConcurrencies() as $k => $v) {
                $concKeys[$rId][$v] = $k;
            }
            foreach ($log->getTestTypes() as $k => $v) {
                $testKeys[$rId][$v] = $k;
            }
            $concurrencies = array_merge($concurrencies, $log->getConcurrencies());
            $testTypes = array_merge($testTypes, $log->getTestTypes());

            $cData = $log->getCheckoutData();
            if ($cData) {
                $checkout[$rId] = $cData;
            }
        }
        $concurrencies = array_unique($concurrencies);
        $testTypes = array_unique($testTypes);

        echo '<script type="text/javascript" src="http://extra.amcharts.com/public/swfobject.js"></script>';


        $amSeriesXml = '<series>';
        foreach ($concurrencies as $cId) {
            $amSeriesXml .= '<value xid=\''.$cId.'\'>'.$cId.'</value>';
        }
        $amSeriesXml .='</series>';

        $amGraphsXml = '<graphs>';
        $gId = 0;
        foreach ($reports as $rId => $log) {
            /* @var $log Performance_Report_Log */
            foreach ($testTypes as $testTypeCode) {
                $amGraphsXml .= '<graph gid=\''.$gId.'\'><title>'.$log->getLogName().' / '.$testTypeCode.'</title><balloon_text><![CDATA[<b>{value}</b> <small>({title})</small>]]></balloon_text><line_width>2</line_width></graph>';
                $gId ++;
            }
        }
        $amGraphsXml .= '</graphs>';

        foreach ($logColumns as $colId => $colName) {
            $amChartId  = $colId;
            $amTitle    = $colName;
            $amDataXml  = '<chart>' . $amSeriesXml;
            $amDataXml .= '<graphs>';

            $gId = 0;
            foreach ($reports as $rId => $log) {
                /* @var $log Performance_Report_Log */
                $logData = $log->getLogData();
                foreach ($testTypes as $typeId => $testTypeCode) {
                    $amDataXml .= '<graph gid=\''.$gId.'\'>';
                    foreach ($concurrencies as $cName) {

                        $tId = isset($testKeys[$rId][$testTypeCode]) ? $testKeys[$rId][$testTypeCode] : -1;
                        $cId = isset($concKeys[$rId][$cName]) ? $concKeys[$rId][$cName] : -1;

                        $val = '';
                        if (isset($logData[$tId][$cId][$colId])) {
                            $val = $logData[$tId][$cId][$colId];
                        }

                        $amDataXml .= '<value xid=\''.$cName.'\'>'.$val.'</value>';
                    }
                    $amDataXml .= '</graph>';
                    $gId ++;
                }
            }

            foreach ($log->getTestTypes() as $typeId => $typeName) {
                $amDataXml .= '<graph gid=\''.$typeId.'\'>';
                foreach ($log->getConcurrencies() as $cId => $cName) {
                    $val = '';
                    if (isset($logData[$typeId][$cId][$colId])) {
                        $val = $logData[$typeId][$cId][$colId];
                    }
                    $amDataXml .= '<value xid=\''.$cId.'\'>'.$val.'</value>';
                }
                $amDataXml .= '</graph>';
            }

            $amDataXml .= '</graphs>';

            echo <<<HTML
<div id="amcharts_{$amChartId}">You need to upgrade your Flash Player</div>
<script type="text/javascript">
    var so = new SWFObject("http://extra.amcharts.com/public/amline.swf", "amline", "900", "400", "8", "#FFFFFF");
    so.addVariable("path", "amline/");
    so.addVariable("chart_settings", encodeURIComponent("<settings><font>Tahoma</font><hide_bullets_count>18</hide_bullets_count><decimals_separator>.</decimals_separator><background><alpha>90</alpha><border_alpha>10</border_alpha></background><plot_area><margins><left>50</left><right>40</right><bottom>65</bottom></margins></plot_area><grid><x><alpha>10</alpha><approx_count>9</approx_count></x><y_left><alpha>10</alpha></y_left></grid><axes><x><width>1</width><color>0D8ECF</color></x><y_left><width>1</width><color>0D8ECF</color></y_left></axes><indicator><color>0D8ECF</color><line_alpha>50</line_alpha><selection_color>0D8ECF</selection_color><selection_alpha>20</selection_alpha></indicator><labels><label lid='0'><text><![CDATA[<b>{$amTitle}</b>]]></text><y>25</y><text_size>13</text_size><align>center</align></label></labels>{$amGraphsXml}<legend><enabled>0</enabled></legend></settings>"));
    so.addVariable("chart_data", encodeURIComponent("{$amDataXml}"));
    so.write("amcharts_{$amChartId}");
</script>
HTML;
        }

        // Compare checkout
        if ($checkout) {
            echo "<br /><h3>Checkout Results (10 minutes)</h3>";

            $amSeriesXml = '<series>';
            $amGraphsXml = "<graphs>";
            $amDataXml    = "<chart>";
            foreach ($concurrencies as $cId) {
                $amSeriesXml .= "<value xid='{$cId}'>{$cId}</value>";
            }
            $amSeriesXml .= "</series>";
            $amDataXml .= $amSeriesXml . "<graphs>";
            foreach ($checkout as $rId => $cData) {
                $amGraphsXml .= "<graph gid='{$rId}'><title>{$reports[$rId]->getLogName()}</title></graph>";
                $amDataXml .= "<graph gid='{$rId}'>";
                foreach ($cData as $cId => $orders) {
                    $amDataXml .= "<value xid='{$cId}'>{$orders}</value>";
                }
                $amDataXml .= "</graph>";
            }
            $amDataXml   .= "</graphs></chart>";
            $amGraphsXml .= "</graphs>";

            echo <<<HTML
            <div id="amcharts_checkout">You need to upgrade your Flash Player</div>
<script type="text/javascript">
    var so = new SWFObject("http://extra.amcharts.com/public/amcolumn.swf", "amcolumn", "900", "300", "8", "#FFFFFF");
    so.addVariable("path", "amcolumn/");
    so.addVariable("chart_settings", encodeURIComponent("<settings><colors>D76357,ADD981,7F8DA9,FEC514,92C7E7</colors><background><alpha>100</alpha><border_alpha>20</border_alpha></background><grid><category><dashed>1</dashed></category><value><dashed>1</dashed></value></grid><axes><category><width>1</width><color>E7E7E7</color></category><value><width>1</width><color>E7E7E7</color></value></axes><values><value><min>0</min></value></values><depth>15</depth><column><width>85</width><balloon_text>{title}: {value} order(s)</balloon_text><grow_time>3</grow_time><grow_effect>regular</grow_effect></column>{$amGraphsXml}<labels><label lid='0'><y>18</y><text_color>000000</text_color><text_size>13</text_size><align>center</align></label></labels></settings>"));
    so.addVariable("chart_data", encodeURIComponent("{$amDataXml}"));
    so.write("amcharts_checkout");
</script>
HTML;
        }
    }
}

class Performance_Report_Log
{
    /**
     * Log file
     *
     * @var string
     */
    protected $_logFile;

    /**
     * Run with concurrencies
     *
     * @var array
     */
    protected $_concurrencies;

    /**
     * Runned test Types
     *
     * @var array
     */
    protected $_testTypes;

    /**
     * Siege log report columns
     *
     * @var array
     */
    protected $_logColumns      = array(
//        0   => 'Date & Time',
//        1   => 'Transactions',
//        2   => 'Elapsed time',
//        3   => 'Data transferred',
        4   => 'Response time (secs)',
        5   => 'Transaction rate (trans/sec)',
        6   => 'Throughput (MB/sec)',
//        7   => 'Concurrency',
        8   => 'Successful transactions',
        9   => 'Failed transactions'
    );

    /**
     * Checkout tests log file
     *
     * @var string
     */
    protected $_checkoutLog;

    /**
     * Parsed Checkout Data
     *
     * @var array
     */
    protected $_checkoutData;

    /**
     * Parsed log data array
     *
     * @var array
     */
    protected $_logData;

    /**
     * Test server name (description)
     *
     * @var string
     */
    protected $_serverName;

    /**
     * Magento CE or EE
     *
     * @var string
     */
    protected $_magentoBuild;

    /**
     * Version of Magento
     *
     * @var string
     */
    protected $_magentoVersion;

    /**
     * Magento data type
     *
     * @var string
     */
    protected $_magentoData;

    /**
     * Additional info about Magento Configuration
     *
     * @var string
     */
    protected $_magentoConfiguration;

    /**
     * Data file with apache configuration
     *
     * @var string
     */
    protected $_apacheConfigFile;

    /**
     * Data file with nginx configuration
     *
     * @var string
     */
    protected $_nginxConfigFile;

    /**
     * Data file with MySQL configuration
     *
     * @var string
     */
    protected $_mysqlConfigFile;

    /**
     * Data file with PHP-FPM configuration
     *
     * @var string
     */
    protected $_phpFpmConfigFile;

    /**
     * Data file with PHP configuration
     *
     * @var string
     */
    protected $_phpConfigFile;

    /**
     * Set Siege report log file
     *
     * @param string $file
     * @return Performance_Report_Log
     */
    public function setLogFile($file)
    {
        $this->_logFile = $file;
        return $this;
    }

    /**
     * Set Run tests with concurrencies
     *
     * @param array $concurrencies
     * @return Performance_Report_Log
     */
    public function setConcurrencies(array $concurrencies)
    {
        $this->_concurrencies = array_values($concurrencies);
        return $this;
    }

    /**
     * Set run tests by types
     *
     * @param array $types
     * @return Performance_Report_Log
     */
    public function setTestTypes(array $types)
    {
        $this->_testTypes = array_values($types);
        return $this;
    }

    /**
     * Set server name and description
     *
     * @param string $name
     * @return Performance_Report_Log
     */
    public function setServerName($name)
    {
        $this->_serverName = $name;
        return $this;
    }

    /**
     * Set Magento version details
     *
     * @param string $type Type of Magento (CE or EE)
     * @param string $version Version of Magento
     * @return Performance_Report_Log
     */
    public function setMagentoVersion($type, $version)
    {
        $this->_magentoBuild = $type;
        $this->_magentoVersion = $version;
        return $this;
    }

    /**
     * Set Magento based on DataBase
     *
     * @param string $data
     * @return Performance_Report_Log
     */
    public function setMagentoData($data)
    {
        $this->_magentoData = $data;
        return $this;
    }

    /**
     * Set additional information about Magento configuration
     *
     * @param string $config
     * @return Performance_Report_Log
     */
    public function setMagentoConfig($config)
    {
        $this->_magentoConfiguration = $config;
        return $this;
    }

    /**
     * Set file contains information about Apache configuration
     *
     * @param string $fileName
     * @return Performance_Report_Log
     */
    public function setApacheConfigFile($fileName)
    {
        $this->_apacheConfigFile = $fileName;
        return $this;
    }

    /**
     * Set file contains information about Nginx configuration
     *
     * @param string $fileName
     * @return Performance_Report_Log
     */
    public function setNginxConfigFile($fileName)
    {
        $this->_nginxConfigFile = $fileName;
        return $this;
    }

    /**
     * Set file contains information about MySQL configuration
     *
     * @param string $fileName
     * @return Performance_Report_Log
     */
    public function setMysqlConfigFile($fileName)
    {
        $this->_mysqlConfigFile = $fileName;
        return $this;
    }

    /**
     * Set file contains information about PHP-FPM configuration
     *
     * @param string $fileName
     * @return Performance_Report_Log
     */
    public function setPhpFpmConfigFile($fileName)
    {
        $this->_phpFpmConfigFile = $fileName;
        return $this;
    }

    /**
     * Set file contains information about PHP configuration
     *
     * @param string $fileName
     * @return Performance_Report_Log
     */
    public function setPhpConfigFile($fileName)
    {
        $this->_phpConfigFile = $fileName;
        return $this;
    }

    /**
     * Set log file with result of checkout test
     *
     * @param string $filename
     * @return Performance_Report_Log
     */
    public function setCheckoutLogFile($filename)
    {
        $this->_checkoutLog = $filename;
        return $this;
    }

    /**
     * Parse siege log file
     *
     * @return Performance_Report_Log
     */
    public function parse()
    {
        // validate log file
        if (empty($this->_logFile)) {
            throw new Exception("Log file is not defined");
        }
        if (!file_exists($this->_logFile) || !is_readable($this->_logFile)) {
            throw new Exception("Log file does not exists or can not readable");
        }

        // validate concurrencies
        if (empty($this->_concurrencies)) {
            throw new Exception("Concurrencies are not defined");
        }

        // validate test types
        if (empty($this->_testTypes)) {
            throw new Exception("Test types are not defined");
        }

        $this->_logData = array();

        $fp = fopen($this->_logFile, 'r');
        if ($fp === false) {
            throw new Exception("failed to open Log file");
        }
        $tt = 0;
        $tc = count($this->_testTypes);
        $i  = 0;
        $ci = 0;
        while (feof($fp) === false) {
            $i ++;
            $csv = fgetcsv($fp);
            if ($i == 1) { // skip header line
                continue;
            }
            if (empty($csv)) { // empty end lines
                continue;
            }

            if ($tt == $tc) {
                $tt = 0;
                $ci ++;
            }

            $this->_logData[$tt][$ci] = $csv;

            $tt ++;
        }

        fclose($fp);

        if (!is_null($this->_checkoutLog)) {
            if (file_exists($this->_checkoutLog) && is_readable($this->_checkoutLog)) {
                $fp = fopen($this->_checkoutLog, 'r');
                if ($fp !== false) {
                    $this->_checkoutData = array();
                    while (feof($fp) === false) {
                        $csv = fgetcsv($fp);
                        if (empty($csv)) {
                            continue;
                        }
                        $this->_checkoutData[$csv[0]] = $csv[1];
                    }
                    fclose($fp);
                }
            }
        }

        return $this;
    }

    /**
     * Return parsed log data array
     *
     * @return array
     */
    public function getLogData()
    {
        return $this->_logData;
    }

    /**
     * Retrieve Log Data columns description
     *
     * @return array
     */
    public function getLogColumns()
    {
        return $this->_logColumns;
    }

    /**
     * Retrieve results array for checkout tests
     *
     * @return array|false
     */
    public function getCheckoutData()
    {
        if (!is_null($this->_checkoutData)) {
            return $this->_checkoutData;
        }
        return false;
    }

    /**
     * Retrieve Concurrencies array
     *
     * @return array
     */
    public function getConcurrencies()
    {
        return $this->_concurrencies;
    }

    /**
     * Retrieve test types array
     *
     * @return array
     */
    public function getTestTypes()
    {
        return $this->_testTypes;
    }

    /**
     * Retrieve Magento Info array
     *
     * @return array
     */
    public function getMagentoInfo()
    {
        return array(
            'version'   => $this->_magentoBuild . ' ' . $this->_magentoVersion,
            'config'    => $this->_magentoConfiguration,
            'data'      => $this->_magentoData,
        );
    }

    /**
     * Retrieve report identity name
     *
     * @return string
     */
    public function getLogName()
    {
        return sprintf('%s Magento %s %s [%s]',
            $this->_serverName,
            $this->_magentoBuild,
            $this->_magentoVersion,
            $this->_magentoData
        );
    }

    /**
     * Retrieve array of info by server
     *
     * @return array
     */
    public function getServerInfo()
    {
        $info = array(
            'name'      =>  $this->_serverName,
            'apache'    => false,
            'nginx'     => false,
            'mysql'     => false,
            'php_fpm'   => false,
            'php'       => false
        );

        if ($this->_apacheConfigFile && is_readable($this->_apacheConfigFile)) {
            $info['apache'] = file_get_contents($this->_apacheConfigFile);
        }
        if ($this->_nginxConfigFile && is_readable($this->_nginxConfigFile)) {
            $info['nginx'] = file_get_contents($this->_nginxConfigFile);
        }
        if ($this->_mysqlConfigFile && is_readable($this->_mysqlConfigFile)) {
            $info['mysql'] = file_get_contents($this->_mysqlConfigFile);
        }
        if ($this->_phpFpmConfigFile && is_readable($this->_phpFpmConfigFile)) {
            $info['php_fpm'] = file_get_contents($this->_phpFpmConfigFile);
        }
        if ($this->_phpConfigFile && is_readable($this->_phpConfigFile)) {
            $info['php'] = file_get_contents($this->_phpConfigFile);
        }

        return $info;
    }
}

class Performance_Report_Action
{
    /**
     * Helper instance
     *
     * @var Performance_Report_Helper
     */
    protected $_helper;

    /**
     * Initialize Controller-Action class
     *
     */
    public function __construct()
    {
        $this->_helper = new Performance_Report_Helper();
    }

    public function indexAction()
    {
        $this->_helper->printHeaderHtml('List of Magento Performace Test Reports');

        $reports = array();
        $reportPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logs';
        if (file_exists($reportPath) && is_dir($reportPath)) {
            foreach (glob($reportPath . DIRECTORY_SEPARATOR . '*') as $filename) {
                if (is_dir($filename) && file_exists($filename . DIRECTORY_SEPARATOR . 'config.xml')) {
                    $xml = simplexml_load_file($filename . DIRECTORY_SEPARATOR . 'config.xml');
                    $reports[basename($filename)] = sprintf('%s Magento %s %s [%s]',
                        $xml->server,
                        $xml->magento_build,
                        $xml->magento_version,
                        $xml->magento_data
                    );
                }
            }
        }

        if (empty($reports)) {
            $this->_helper->printContentMessage('Sorry, Reports are not available');
        } else {
            $this->_helper->printListOfReports($reports);
        }

        $this->_helper->printFooterHtml();
    }

    /**
     * Retrieve log object by report code
     *
     * @param string $reportId
     * @return Performance_Report_Log|false
     */
    protected function _getLog($reportId)
    {
        if (!$reportId || !preg_match('#^[a-zA-Z0-9-_]+$#', $reportId)) {
            return false;
        }

        $rPath   = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $reportId . DIRECTORY_SEPARATOR;
        $xmlFile = $rPath . 'config.xml';
        if (!file_exists($xmlFile) || !is_readable($xmlFile)) {
            return false;
        }

        $xml = @simplexml_load_file($xmlFile);
        if (!$xml) {
            return false;
        }

        $log = new Performance_Report_Log();

        $log->setServerName((string)$xml->server);
        $log->setMagentoVersion((string)$xml->magento_build, (string)$xml->magento_version);
        $log->setMagentoData((string)$xml->magento_data);
        if ($xml->magento_config) {
            $log->setMagentoConfig((string)$xml->magento_config);
        }

        if ($xml->log_file) {
            $log->setLogFile($rPath . (string)$xml->log_file);
        }
        if ($xml->concurrencies) {
            $concurrencies = array();
            foreach ($xml->concurrencies->children() as $child) {
                $concurrencies[] = (int)$child;
            }
            $log->setConcurrencies($concurrencies);
        }
        if ($xml->test_types) {
            $testTypes = array();
            foreach ($xml->test_types->children() as $child) {
                $testTypes[] = (string)$child;
            }
            $log->setTestTypes($testTypes);
        }

        // checkout log file
        if ($xml->checkout) {
            $log->setCheckoutLogFile($rPath . (string)$xml->checkout);
        }

        // server variables
        if ($xml->apache_conf) {
            $log->setApacheConfigFile($rPath . (string)$xml->apache_conf);
        }
        if ($xml->nginx_conf) {
            $log->setNginxConfigFile($rPath . (string)$xml->nginx_conf);
        }
        if ($xml->mysql_conf) {
            $log->setMysqlConfigFile($rPath . (string)$xml->mysql_conf);
        }
        if ($xml->php_fpm_conf) {
            $log->setPhpFpmConfigFile($rPath . (string)$xml->php_fpm_conf);
        }
        if ($xml->php_conf) {
            $log->setPhpConfigFile($rPath . (string)$xml->php_conf);
        }

        $log->parse();

        return $log;
    }

    public function viewAction()
    {
        $rId = isset($_GET['r']) ? $_GET['r'] : false;
        $log = $this->_getLog($rId);

        if (!$log) {
            $this->norouteAction();
            return ;
        }

        $srvInfo    = $log->getServerInfo();
        $mageInfo   = $log->getMagentoInfo();

        $pageTitle = sprintf('Performance Test on %s for Magento %s with %s',
            $srvInfo['name'],
            $mageInfo['version'],
            $mageInfo['data']
        );

        $this->_helper->printHeaderHtml($pageTitle);
        $this->_helper->printLog($log);
        $this->_helper->printContentMessage('<center><a href="'.$_SERVER['PHP_SELF'].'">Back to List of Reports</a></center>');
        $this->_helper->printFooterHtml();
    }

    public function fetchAction()
    {
        $rId = isset($_GET['r']) ? $_GET['r'] : false;
        $log = $this->_getLog($rId);

        if (!$log) {
            $this->norouteAction();
            return ;
        }
    
        $this->_helper->printLog($log);
    }

    public function compareAction()
    {
        $this->_helper->printHeaderHtml('Compare results');
        $reports = array();
        if (!empty($_POST['reports'])) {
            foreach ($_POST['reports'] as $r) {
                $log = $this->_getLog($r);
                if ($log) {
                    $reports[$r] = $log;
                }
            }
        }

        if (count($reports) > 1) {
            $this->_helper->printCompare($reports);
        } else {
            $this->_helper->printHeaderHtml('Please select reports for compare');
        }

        $this->_helper->printContentMessage('<center><a href="'.$_SERVER['PHP_SELF'].'">Back to List of Reports</a></center>');

        $this->_helper->printFooterHtml();
    }

    public function norouteAction()
    {
        $this->_helper->printHeaderHtml('404 error: Page not found.');
        $this->_helper->printContentMessage('Return to <a href="'.$_SERVER['PHP_SELF'].'">List of Magento Performace Test Reports</a>');
        $this->_helper->printFooterHtml();
    }

    /**
     * Run action
     *
     * @return Tools_Db_Repair_Action
     */
    public function run()
    {
        if (empty($_GET['act'])) {
            $_GET['act'] = 'index';
        }
        $method = $_GET['act'] . 'Action';
        if (!method_exists($this, $method)) {
            $method = 'norouteAction';
        }

        // run action
        $this->$method();
    }
}

