#!/usr/bin/env python

import os, sys
from ConfigParser import SafeConfigParser
import time
import re
from optparse import OptionParser
import shutil

class magentoTest:
    __code              = None
    __concurrencies     = []
    __mode              = None
    
    __test_homepage     = False
    __test_session      = False
    __test_checkout     = False
    
    __time_homepage     = '1M'
    __time_session      = '1M'
    __time_checkout     = '1M'
    
    __urls_homepage     = None
    __urls_session      = None
    __urls_checkout     = None
    
    __session_amount    = 1
    __session_map       = None
    
    __clean_sql_file    = None
    __checkout_orders   = 0
    
    __home              = None
    __reportOutput      = None
    __base_url          = None
    __ssh_keys          = {}
    
    __report            = {}
    
    __bin_siege         = '/usr/bin/siege'
    __bin_mysql         = '/usr/bin/mysql'
    __bin_ssh           = '/usr/bin/ssh'
    
    __cfg_siegerc       = None
    __log_siege         = None
    __log_checkout      = None
    __concurrencies     = None
    
    __restart_httpd     = '/sbin/service httpd restart'
    __restart_mysqld    = '/sbin/service mysqld restart'
    
    __web_hostname      = None
    __web_magepath      = None
    __web_wwwroot       = None
    __web_ssh_key       = None
    __web_ssh_user      = 'root'
    __web_ssh_sudo      = False
    __web_restart       = False
    
    __db_hostname       = 'localhost'
    __db_username       = 'root'
    __db_password       = None
    __db_database       = 'test'
    __db_ssh_key        = None
    __db_ssh_user       = 'root'
    __db_ssh_sudo       = False
    __db_restart        = False
    
    def __init__(self):
        parser = OptionParser()
        parser.add_option('-c', '--config', metavar='FILE', help='use config file')
        parser.add_option('-m', '--mode', default='run', choices=('run','report'), help='test mode: one of \'run\', \'report\' [default: %default]')
        parser.add_option('-o', '--output', help='path for report output directory')
        (options, args) = parser.parse_args()
        if options.config == None:
            parser.print_help()
            print ''
            print 'Error: config file is required option'
            exit(1)
        
        if os.path.exists(options.config) != 0:
            cfgFile = options.config
        else:
            cfgFile = '%s%s%s' % (options.path, os.sep, options.config)
            
        if os.path.exists(cfgFile) == 0:
            print 'Error: Invalid config file "%s"' % (options.config)
            exit(1)
        
        self.__mode = options.mode
            
        config = SafeConfigParser()
        config.read([cfgFile])
        
        self.__home = os.getcwd()
        if options.output != None and os.path.exists(options.output):
            self.__reportOutput = options.output
        else:
            self.__reportOutput = os.getcwd()
        
        options = dict(config.items('options'))
        sshkeys = dict(config.items('sshkeys'))
        mysql   = dict(config.items('mysql'))
        web     = dict(config.items('web'))
        report  = dict(config.items('report'))
        
        # define code
        if 'code' in options == 0:
            print 'Error: Invalid config data'
            exit(1)
        self.__code = options['code']
        
        self.__parseSshKeys(sshkeys)
        self.__parseWebOptions(web)
        self.__parseMysqlOptions(mysql)
        self.__parseOptions(options)
        
        self.__parseTestHomepage(options)
        self.__parseTestSession(options)
        self.__parseTestCheckout(options)
        
        self.__parseReportSection(report)

    def __parseWebOptions(self, web):
        if 'hostname' in web:
            self.__web_hostname = web['hostname']
        if 'magepath' in web:
            self.__web_magepath = web['magepath']
        if 'wwwroot' in web:
            self.__web_wwwroot = web['wwwroot']
        if 'sshkey' in web and web['sshkey'] in self.__ssh_keys:
            self.__web_ssh_key = self.__ssh_keys[web['sshkey']]
        if 'sshuser' in web:
            self.__web_ssh_user = web['sshuser']
        if 'sudo' in web and web['sudo'] in ('true', 'Y', 'y', 'yes'):
            self.__web_ssh_sudo = True
        if 'restart' in web and web['restart'] in ('true', 'Y', 'y', 'yes'):
            self.__web_restart = True
        
    def __parseMysqlOptions(self, mysql):
        if 'hostname' in mysql:
            self.__db_hostname = mysql['hostname']
        if 'database' in mysql:
            self.__db_database = mysql['database']
        if 'username' in mysql:
            self.__db_username = mysql['username']
        if 'password' in mysql and mysql['password'] != '':
            self.__db_password = mysql['password']
        if 'sshkey' in mysql and mysql['sshkey'] in self.__ssh_keys:
            self.__db_ssh_key = self.__ssh_keys[mysql['sshkey']]
        if 'sshuser' in mysql:
            self.__db_ssh_user = mysql['sshuser']
        if 'sudo' in mysql and mysql['sudo'] in ('true', 'Y', 'y', 'yes'):
            self.__db_ssh_sudo = True
        if 'restart' in mysql and mysql['restart'] in ('true', 'Y', 'y', 'yes'):
            self.__db_restart = True
        
    def __parseOptions(self, options):
        self.__concurrencies = options['concurrencies'].split(',')
        self.__base_url = 'http://%s/' % (self.__web_hostname)
        if self.__web_magepath != '' and self.__web_magepath != None:
            self.__base_url = '%s%s/' % (self.__base_url, self.__web_magepath)
        
        # define siege log file
        self.__log_siege = '%s%ssiege_%s.log' % (self.__home, os.sep, self.__code)
        if self.getMode() == 'run' and os.path.exists(self.__log_siege) == 1:
            os.unlink(self.__log_siege)
        
        # siege config
        if 'siege_config' in options == 0:
            print 'Invalid siege config data'
            exit(1)
            
        if self.getMode() == 'run':
            filename = '%s%s.siegerc_%s' % (self.__home, os.sep, self.__code)
            data = options['siege_config'] % (self.__log_siege)
            if os.path.exists(filename) == 1:
                os.unlink(filename)
                
            try:
                f = open(filename, 'w');
                f.write(data);
                f.close()
                
                os.chmod(filename, 0644)
                self.__cfg_siegerc = filename
            except:
                print 'Problem with write siege config file "%s"' % (filename)
                exit(1)

    def __parseReportSection(self, report):
        if self.getMode() == 'run':
            return self

        if 'server' in report:
            self.__report['server_name'] = report['server']
        else:
            if self.__db_hostname == self.__web_hostname:
                name = self.__db_hostname
            else:
                name = '%s+%s' % (self.__web_hostname, self.__db_hostname)
            self.__report['server_name'] = name
            
        if 'magento_build' in report:
            self.__report['magento_build'] = report['magento_build']
        else:
            self.__report['magento_build'] = 'UNDEFINED'
            
        if 'magento_version' in report:
            self.__report['magento_version'] = report['magento_version']
        else:
            self.__report['magento_version'] = ''
            
        if 'magento_data' in report:
            self.__report['magento_data'] = report['magento_data']
        else:
            self.__report['magento_data'] = 'unknown'
            
        self.__report['magento_config'] = ''
        if 'magento_config' in report:
            self.__report['magento_config'] = report['magento_config']
            
        if 'magento_config_urls' in report:
            self.__report['magento_config_urls'] = report['magento_config_urls']
            
        test_type_names = {}
        test_codes = ('homepage', 'session', 'checkout')
        for code in test_codes:
            key = 'name_%s' % (code)
            if key in report:
                name = report[key]
            else:
                name = code
            
            test_type_names[code] = name
        
        if self.__session_amount > 1:
            for i in range(self.__session_amount):
                code = 'session_%d' % (i)
                if code in report:
                    name = report[code]
                else:
                    name = test_type_names['session'] + ' (' + str(i + 1) + ')'
                    
                test_type_names[code] = name
                
        self.__report['test_type_names'] = test_type_names
        
        # config files
        configs = ('apache_conf', 'php_conf', 'mysql_conf', 'nginx_conf', 'php_fpm_conf')
        for config in configs:
            if config in report:
                self.__report[config] = report[config]
            else:
                self.__report[config] = None

    def __parseTestHomepage(self, options):
        if 'test_homepage' in options and options['test_homepage'] in ('true', 'Y', 'y', 'yes'):
            self.__test_homepage = True
            if 'time_homepage' in options:
                self.__time_homepage = options['time_homepage']
            if (self.getMode() == 'run'):
                data = '%s' % self.__base_url
                filename = '%s%surl_home_%s.txt' % (self.__home, os.sep, self.__code)
                if os.path.exists(filename) == 1:
                    os.unlink(filename)
                
                try:
                    f = open(filename, 'w');
                    f.write(data);
                    f.close()
                
                    os.chmod(filename, 0644)
                    self.__urls_homepage = filename
                except:
                    print 'Problem with write homepage test URLs file "%s"' % (filename)
                    exit(1)
            else:
                self.__urls_homepage = '${BASEURL}'
    
    def __parseTestSession(self, options):
        if 'test_session' in options and options['test_session'] in ('true', 'Y', 'y', 'yes'):
            self.__test_session = True
            if 'time_session' in options:
                self.__time_session = options['time_session']

            if 'session_amount' in options and int(options['session_amount']) > 1:
                self.__session_amount = int(options['session_amount'])

            if self.__session_amount == 1 and 'urls_session' in options == 0:
                print 'Please define session URLs list'
                exit(1)
                
            # define session lists
            self.__urls_session = {}
            url_list = {};
            
            if self.__session_amount == 1:
                url_list[url_list.__len__()] = options['urls_session'];
            else:
                self.__session_map = {}
                for i in range(self.__session_amount):
                    url_list_key = 'urls_session_%d' % (i + 1)
                    if url_list_key in options:
                        url_list[url_list.__len__()] = options[url_list_key];
                        self.__session_map[i] = url_list.__len__() - 1
                    else:
                        self.__session_map[i] = None
            # validate session list
            if url_list.__len__() < 1:
                print 'Please define session URLs list(s)'
                exit(1)
                
            if self.getMode() == 'run':
                for (i, data) in url_list.items():
                    data = data.replace("${BASEURL}", self.__base_url)
                    filename = '%s%surl_sess_%s_%d.txt' % (self.__home, os.sep, self.__code, i)
                    if os.path.exists(filename) == 1:
                        os.unlink(filename)
                        
                    try:
                        f = open(filename, 'w');
                        f.write(data);
                        f.close()
                    
                        os.chmod(filename, 0644)
                        self.__urls_session[i] = filename
                    except:
                        print 'Problem with write session test URLs file "%s"' % (filename)
                        exit(1)
            else:
                self.__urls_session = url_list
    
    def __parseTestCheckout(self, options):
        if 'test_checkout' in options and options['test_checkout'] in ('true', 'Y', 'y', 'yes'):
            self.__test_checkout = True
            if 'time_checkout' in options:
                self.__time_checkout = options['time_checkout']
            if 'urls_checkout' in options == 0:
                print 'Please define checkout URLs list'
                exit(1)
                
            if 'checkout_count_order' in options:
                self.__checkout_orders = int(options[checkout_count_order])
                
            if self.getMode() == 'run':
                data = options['urls_checkout'].replace("${BASEURL}", self.__base_url)
                filename = '%s%surl_checkout_%s.txt' % (self.__home, os.sep, self.__code)
                if os.path.exists(filename) == 1:
                    os.unlink(filename)
                
                try:
                    f = open(filename, 'w');
                    f.write(data);
                    f.close()
                
                    os.chmod(filename, 0644)
                    self.__urls_checkout = filename
                except:
                    print 'Problem with write checkout test URLs file "%s"' % (filename)
                    exit(1)
                
                if 'clean_sql_data' in options:
                    filename = '%s%sclean_%s.sql' % (self.__home, os.sep, self.__code)
                    data = options['clean_sql_data']
                    
                    if os.path.exists(filename) == 1:
                        os.unlink(filename)
                        
                    try:
                        f = open(filename, 'w');
                        f.write(data);
                        f.close()
                    
                        os.chmod(filename, 0644)
                        self.__clean_sql_file = filename
                    except:
                        print 'Problem with write cleaning SQL file "%s"' % (filename)
                        exit(1)
                        
            else:
                self.__urls_checkout = options['urls_checkout']
                    
            self.__log_checkout = '%s%scheckout_%s.log' % (self.__home, os.sep, self.__code)
            if self.getMode() == 'run' and os.path.exists(self.__log_checkout) == 1:
                os.unlink(self.__log_checkout)
    
    def __parseSshKeys(self, sshkeys):
        if self.getMode() == 'run':
            for k, v in sshkeys.iteritems():
                filename = '%s%s.%s_%s.key' % (self.__home, os.sep, k, self.__code)
                if os.path.exists(filename) == 1:
                    os.unlink(filename)
                try:
                    f = open(filename, 'w');
                    f.write(v);
                    f.write("\n", )
                    f.close()
                
                    os.chmod(filename, 0600)
                    
                    self.__ssh_keys[k] = filename
                except:
                    print 'Problem with write ssh key "%s"' % (filename)
                    exit(1)
                
    def restartMysqld(self):
        if self.__db_restart == False:
            return
        ssh_cmd = self.__bin_ssh + ' -o StrictHostKeyChecking=no'
        if self.__db_ssh_key != None:
            ssh_cmd = ssh_cmd + ' -t -i ' + self.__db_ssh_key
        ssh_host = ' %s@%s' % (self.__db_ssh_user, self.__db_hostname)
        ssh_cmd = ssh_cmd + ssh_host
        
        if self.__db_ssh_sudo == True:
            ssh_cmd = ssh_cmd + ' sudo'
        
        ssh_cmd = ssh_cmd + ' ' + self.__restart_mysqld
        
        print '# RESTARTING MYSQL SERVER'
        if os.system(ssh_cmd) != 0:
            print 'Can\'t restart MySQL server'
            exit();
            
        time.sleep(10)
        
    def restartHttpd(self):
        if self.__web_restart == False:
            return
        ssh_cmd = self.__bin_ssh + ' -o StrictHostKeyChecking=no'
        if self.__web_ssh_key != None:
            ssh_cmd = ssh_cmd + ' -t -i ' + self.__web_ssh_key
        ssh_host = ' %s@%s' % (self.__web_ssh_user, self.__web_hostname)
        ssh_cmd = ssh_cmd + ssh_host
        
        if self.__web_ssh_sudo == True:
            ssh_cmd = ssh_cmd + ' sudo'
        
        ssh_restart_cmd = ssh_cmd + ' ' + self.__restart_httpd
        
        print '# RESTARTING HTTPD SERVER'
        if os.system(ssh_restart_cmd) != 0:
            print 'Can\'t restart HTTPD server'
            exit();
            
        # remove cache
        if self.__web_wwwroot != None:
            path = self.__web_wwwroot
            if self.__web_magepath != None and self.__web_magepath != '':
                path = '%s/%s' % (path, self.__web_magepath)
            ssh_rm_cmd = '%s rm -rf %s/var/cache' % (ssh_cmd, path)
            
            print '# REMOVE THE FILE CACHE'
            if os.system(ssh_rm_cmd) != 0:
                print 'Can\'t remove the file cache'
                exit();
        
    def __sleepAfterTest(self, concurrency):
        sleep = int(concurrency) * 1
        if sleep > 300:
            sleep = 300
        return sleep
        
    def runTestHomepage(self, concurrency):
        if self.__test_homepage == False:
            return self

        print '# TEST HOMEPAGE, TIME=' + str(self.__time_homepage)
        
        if self.__web_restart == True:
            self.restartHttpd()
            time.sleep(5)
            
            print '# PREPARING MAGENTO CACHE'
            siege_cmd = '%s -c 1 -v -f %s --reps once' % (self.__bin_siege, self.__urls_homepage)
            print siege_cmd
            os.system(siege_cmd)
            time.sleep(5)
        
        print '# RUNNING HOMEPAGE TEST, TIME=' + str(self.__time_homepage) + ', CONCURRENCY=' + concurrency
        siege_cmd = '%s -R %s -c %s -t %s -f %s' % (self.__bin_siege, self.__cfg_siegerc, concurrency, self.__time_homepage, self.__urls_homepage)
        os.system(siege_cmd)
        
        sleep = self.__sleepAfterTest(concurrency)
        print '# GOING TO SLEEP FOR %d SECONDS' % (sleep)
        time.sleep(sleep)
        
    def runTestSession(self, concurrency):
        if self.__test_session == False:
            return self

        for (i, filename) in self.__urls_session.items():
            print '# TEST SESSION [' + str(i + 1) + '], TIME=' + str(self.__time_session)
            
            if self.__web_restart == True:
                self.restartHttpd()
                time.sleep(5)
            
                print '# PREPARING MAGENTO CACHE SESSION [' + str(i + 1) + ']'
                siege_cmd = '%s -c 1 -v -f %s --reps once' % (self.__bin_siege, filename)
                os.system(siege_cmd)
                time.sleep(5)
            
            print '# RUNNING SESSION [' + str(i + 1) + '] TEST, TIME=' + str(self.__time_session) + ', CONCURRENCY=' + concurrency
            siege_cmd = '%s -R %s -c %s -t %s -f %s' % (self.__bin_siege, self.__cfg_siegerc, concurrency, self.__time_session, filename)
            os.system(siege_cmd)
            
            sleep = self.__sleepAfterTest(concurrency)
            print '# GOING TO SLEEP FOR %d SECONDS' % (sleep)
            time.sleep(sleep)
    
    def runTestCheckout(self, concurrency):
        if self.__test_checkout == False:
            return self
        
        mysql_cmd = '%s -h %s -u%s' % (self.__bin_mysql, self.__db_hostname, self.__db_username)
        if self.__db_password != None:
            mysql_cmd = mysql_cmd + ' -p' + self.__db_password
        mysql_cmd = mysql_cmd + ' -D ' + self.__db_database
        
        print '# TEST CHECKOUT, TIME=' + str(self.__time_checkout)
        
        if self.__web_restart == True:
            self.restartHttpd()
            time.sleep(5)
        
        if self.__clean_sql_file != None:
            print '# CLEANING MAGENTO DATABASE BEFORE TEST'
            clean_cmd = '%s < %s' %(mysql_cmd, self.__clean_sql_file)
            
            os.system(clean_cmd)
            time.sleep(5)
        
        if self.__web_restart == True:
            print '# PREPARING MAGENTO CACHE'
            siege_cmd = '%s -c 1 -v --reps once %s' % (self.__bin_siege, self.__base_url)
            os.system(siege_cmd)
            time.sleep(5)
        
        print '# RUNNING CHECKOUT TEST, TIME=' + str(self.__time_checkout) + ', CONCURRENCY=' + concurrency
        siege_cmd = '%s -R %s -c %s -t %s -f %s' % (self.__bin_siege, self.__cfg_siegerc, concurrency, self.__time_checkout, self.__urls_checkout)
        os.system(siege_cmd)
        
        sleep = self.__sleepAfterTest(concurrency)
        print '# GOING TO SLEEP FOR %d SECONDS' % (sleep)
        time.sleep(sleep)
        
        filename = '%s%s%s.mysql' % (self.__home, os.sep, self.__code)
        query = 'SELECT COUNT(*) as cnt FROM sales_flat_order'
        order_cmd = '%s -e \'%s\' > %s' % (mysql_cmd, query, filename)
        os.system(order_cmd)
        
        f = open(filename, 'r')
        content = f.read()
        f.close()
        os.unlink(filename)
        
        orders = 0
        match = re.findall(r'\d+', content)
        if len(match) > 0:
            orders = int(match[0]) - self.__checkout_orders
        
        checkout_csv = '%d,%d\n' % (int(concurrency), orders)
        f = open(self.__log_checkout, 'a+')
        f.seek(0, 2)
        f.write(checkout_csv)
        f.close()
        
        print '# CREATED %d ORDER(S)' % orders

    def getMode(self):
        return self.__mode

    def run(self):
        self._beforeRun()
        for concurrency in self.__concurrencies:
            print '###############################################################################'
            print '# CONCURRENCY: ' + str(concurrency)
            print '###############################################################################'
            
            self.runTestHomepage(concurrency)
            self.runTestSession(concurrency)
            self.runTestCheckout(concurrency)
        
        self._afterRun()
        
    def _beforeRun(self):
        self.restartMysqld()

    def _afterRun(self):
        # Remove server keys
        for k, v in self.__ssh_keys.iteritems():
            os.unlink(v)
            
        os.unlink(self.__cfg_siegerc)
        
        if self.__test_homepage and self.__urls_homepage != None:
            os.unlink(self.__urls_homepage)
        
        if self.__test_session and self.__urls_session != None:
            os.unlink(self.__urls_session)
        
        if self.__test_checkout and self.__urls_checkout != None:
            os.unlink(self.__urls_checkout)
            
        if self.__test_checkout and self.__clean_sql_file != None:
            os.unlink(self.__clean_sql_file)
        
    def createReport(self):
        path = '%s%s%s' % (self.__reportOutput, os.sep, self.__code)
        if os.path.exists(path) == 0:
            os.mkdir(path)
            os.chmod(path, 0755)
            
        xml_file = '%s%sconfig.xml' % (path, os.sep)
        
        magento_config = self.__report['magento_config']
        checkout_log   = ''
        xml_test_types = ''
        concurrencies  = ''
        
        if 'magento_config_urls' in self.__report:
            if self.__report['magento_config_urls'] in ('Y','yes','true','1'):
                magento_config_urls = True
            else:
                magento_config_urls = False
        else:
            magento_config_urls = True
        
        for concurency in self.__concurrencies:
            node = '        <concurrency>%s</concurrency>\n' % (concurency)
            concurrencies = concurrencies + node
        
        if self.__test_homepage == True:
            node = '        <type>%s</type>\n' % (self.__report['test_type_names']['homepage'])
            xml_test_types = xml_test_types + node
        if self.__test_session == True:
            for i in range(self.__session_amount):
                if magento_config_urls == True:
                    if self.__session_amount == 1:
                        key = 'session'
                        j   = 0
                    else:
                        if self.__session_map[i] == None:
                            continue
                        j   = self.__session_map[i]
                        key = 'session_%d' % (i)
                        
                    config = '<b>%s</b><br/><pre>%s</pre><br/>' % (self.__report['test_type_names'][key], self.__urls_session[j])
                    magento_config = magento_config + config
                node = '        <type>%s</type>\n' % (self.__report['test_type_names'][key])
                xml_test_types = xml_test_types + node
        
        if self.__test_checkout == True:
            if magento_config_urls == True:
                config = '<b>%s</b><br/><pre>%s</pre><br/>' % (self.__report['test_type_names']['checkout'], self.__urls_checkout)
                magento_config = magento_config + config
            node = '        <type>%s</type>\n' % (self.__report['test_type_names']['checkout'])
            xml_test_types = xml_test_types + node
            
            checkout_log = '    <checkout>checkout.log</checkout>\n'
        
        xml_config_files = ''
        config_files = ('apache_conf', 'php_conf', 'mysql_conf', 'nginx_conf', 'php_fpm_conf')
        for config_file in config_files:
            if config_file in self.__report and self.__report[config_file] != None and self.__report[config_file] != '':
                source = '%s%s%s' % (self.__home, os.sep, self.__report[config_file])
                target = '%s%s%s' % (path, os.sep, self.__report[config_file])
                if os.path.exists(source):
                    shutil.copy(source, target)
                    os.chmod(target, 0644)
                    node = '    <%s>%s</%s>\n' % (config_file, self.__report[config_file], config_file)
                    xml_config_files = xml_config_files + node
        
        xml_data = """<?xml version="1.0"?>
<config>
    <server>%s</server>
    <magento_build>%s</magento_build>
    <magento_version>%s</magento_version>
    <magento_data>%s</magento_data>
    <magento_config><![CDATA[%s]]></magento_config>
    <log_file>siege.log</log_file>
%s    <concurrencies>
%s    </concurrencies>
    <test_types>
%s    </test_types>
%s</config>
""" % (self.__report['server_name'], self.__report['magento_build'], self.__report['magento_version'],
       self.__report['magento_data'], magento_config, checkout_log, concurrencies, xml_test_types, xml_config_files)

        f = open(xml_file, 'w')
        f.write(xml_data)
        f.close()
        os.chmod(xml_file, 0644)

        if self.__log_siege != None and os.path.exists(self.__log_siege):
            target = '%s%s%s' % (path, os.sep, 'siege.log')
            shutil.copy(self.__log_siege, target)
            os.chmod(target, 0644)
            
        if self.__log_checkout != None and os.path.exists(self.__log_checkout):
            target = '%s%s%s' % (path, os.sep, 'checkout.log')
            shutil.copy(self.__log_checkout, target)
            os.chmod(target, 0644)

        print ''
        print 'Report successfully created in %s' % (path)
        
test = magentoTest()
if test.getMode() == 'run':
    test.run()
if test.getMode() == 'report':
    test.createReport()
