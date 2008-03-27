#!/bin/sh

MAGE_SVN_ROOT=http://svn.magentocommerce.com/svn/magento/base/magento/trunk
MAGE_LOCAL_ROOT="."

for ENTRY in downloader index.php .htaccess LICENSE.txt favicon.ico
do
  #echo $ENTRY
  svn export --force $MAGE_SVN_ROOT/$ENTRY $MAGE_LOCAL_ROOT/$ENTRY
done

mkdir $MAGE_LOCAL_ROOT/media $MAGE_LOCAL_ROOT/var
chmod 777 -R $MAGE_LOCAL_ROOT
