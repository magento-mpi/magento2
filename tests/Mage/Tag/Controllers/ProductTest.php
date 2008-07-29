<?php
if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../../PHPUnitTestInit.php';
    $suite = new PHPUnit_Framework_TestSuite();
    $suite->addTestSuite('Mage_Tag_Controllers_ProductTest');
    PHPUnit_TextUI_TestRunner::run($suite);
    exit;
}

class Mage_Tag_Controllers_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testListAction()
    {
        ob_start();
        foreach (array(
          'UNIQUE_ID' => 'EgNE8cCoAAEAAML7G5wAAAAO',
          'HTTP_AUTHORIZATION' => '',
          'HTTP_HOST' => 'kv.no-ip.org',
          'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1',
          'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
          'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
          'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
          'HTTP_KEEP_ALIVE' => '300',
          'HTTP_CONNECTION' => 'keep-alive',
          'HTTP_REFERER' => 'http://kv.no-ip.org/dev/anton.makarenko/magento_trunk/index.php/simple/index.html',
          'HTTP_COOKIE' => 'store=default; magento=7rc2dvsm5cabhtowvgzy5mdj9hw6fzle; frontend=dbb24f4438ce418599a105c888932539; hackerConsole=0',
          'HTTP_CACHE_CONTROL' => 'max-age=0',
          'PATH' => '/sbin:/bin:/usr/sbin:/usr/bin:/usr/games:/usr/local/sbin:/usr/local/bin:/root/bin',
          'SERVER_SIGNATURE' => '<address>Apache/2.2.8 (FreeBSD) mod_ssl/2.2.8 OpenSSL/0.9.8g DAV/2 SVN/1.4.6 PHP/5.2.5 Server at kv.no-ip.org Port 80</address>',
          'SERVER_SOFTWARE' => 'Apache/2.2.8 (FreeBSD) mod_ssl/2.2.8 OpenSSL/0.9.8g DAV/2 SVN/1.4.6 PHP/5.2.5',
          'SERVER_NAME' => 'kv.no-ip.org',
          'SERVER_ADDR' => '192.168.0.191',
          'SERVER_PORT' => '80',
          'REMOTE_ADDR' => '192.168.0.101',
          'DOCUMENT_ROOT' => '/usr/local/www/apache22/data-php525',
          'SERVER_ADMIN' => 'michael@varien.com',
          'SCRIPT_FILENAME' => '/usr/local/www/apache22/data-php525/dev/anton.makarenko/magento_trunk/index.php',
          'REMOTE_PORT' => '3695',
          'GATEWAY_INTERFACE' => 'CGI/1.1',
          'SERVER_PROTOCOL' => 'HTTP/1.1',
          'REQUEST_METHOD' => 'GET',
          'QUERY_STRING' => '',
          'REQUEST_URI' => '/dev/anton.makarenko/magento_trunk/index.php/tag/product/list/tagId/11/',
          'SCRIPT_NAME' => '/dev/anton.makarenko/magento_trunk/index.php',
          'PATH_INFO' => '/tag/product/list/tagId/11/',
          'PATH_TRANSLATED' => '/usr/local/www/apache22/data-php525/tag/product/list/tagId/11/',
          'PHP_SELF' => '/dev/anton.makarenko/magento_trunk/index.php/tag/product/list/tagId/11/',
          'REQUEST_TIME' => 1217352947,
          'argv' => array (),
          'argc' => 0,
        ) as $key => $value) {
            $_SERVER[$key] = $value;
        }

        Mage::app()->getFrontController()->getRequest()->setParam('tagId', '11');
        Mage::app()->getFrontController()->dispatch();

        $contents = ob_get_clean();
        $this->assertContains('Скуби-ду, the', $contents);
    }
}
