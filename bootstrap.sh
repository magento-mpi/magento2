#!/bin/bash

MAGE_SVN_ROOT=http://svn.magentocommerce.com/svn/magento/base/magento/trunk

for ENTRY in 'index.php pear LICENSE.txt favicon.ico .htaccess
  lib app/etc app/Mage.php app/code/core/Mage/Core app/code/core/Mage/Install
  js/prototype js/mage js/spacer.gif js/blank.html
  app/design/install skin/install'
do
    svn export $MAGE_SVN_ROOT/$ENTRY ./$ENTRY
done
exit;

svn export $MAGE_SVN_ROOT/index.php
svn export $MAGE_SVN_ROOT/pear
svn export $MAGE_SVN_ROOT/LICENSE.txt
svn export $MAGE_SVN_ROOT/favicon.ico
svn export $MAGE_SVN_ROOT/.htaccess

svn export $MAGE_SVN_ROOT/lib lib
svn export $MAGE_SVN_ROOT/app/etc app/etc
svn export $MAGE_SVN_ROOT/app/Mage.php app/Mage.php
svn export $MAGE_SVN_ROOT/app/code/core/Mage/Core app/code/core/Mage/Core
svn export $MAGE_SVN_ROOT/app/code/core/Mage/Install app/code/core/Mage/Install
svn export $MAGE_SVN_ROOT/js/prototype js/prototype
svn export $MAGE_SVN_ROOT/js/mage js/mage
svn export $MAGE_SVN_ROOT/js/spacer.gif js/spacer.gif
svn export $MAGE_SVN_ROOT/js/blank.html js/blank.html
svn export $MAGE_SVN_ROOT/app/design/install app/design/install
svn export $MAGE_SVN_ROOT/skin/install skin/install
