#!/bin/sh
#-x
E_BADARGS=11
RELEASE_DIR=./release
SOURCE_DIR=./source
SVN_LOGIN="alex.bomko"
SVN_PASSWORD="Kdu7(hju"
TRUNK_URL="http://svn.magentocommerce.com/svn/magento/base/magento/trunk"

rOptions=""

usage() {
 if [ ! -n "$2" ]; then
  echo "Usage: "
  echo " `basename $0` [options] <command> [params]"
  echo "Ex.:"
  echo " `basename $0` create ee 1.10.1.0 ./release/ee/1.10.1.0 "\
       "http://svn.magentocommerce.com/svn/magento/base/magento/qa/enterprise/tags/1.10.0.0"
  echo; echo "To show datailed command parameters use"
  echo " `basename $0` help <command>"
  echo "Available commands is:"
  echo " create";echo " upload"
 else
  case "$2" in
   create)
    echo "Create release extension packages"
    echo "Params:"
    echo " version   - ce, ee, pe"
    echo " release   - release to be created for ex. 1.10.1.0"
    echo " path      - root of magento sources directory"
    echo " svn       - SVN link to get sources, optional"
    echo; echo "Example:"
    echo " `basename $0` create ee 1.10.1.0 ./release/ee/1.10.1.0 "\
     "http://svn.magentocommerce.com/svn/magento/base/magento/qa/enterprise/tags/1.10.0.0"
    ;;
   upload)
    echo "Upload release extension packages from path/var/connect/ directory  to channel server"
    echo "Params:"
    echo " version   - ce, ee, pe"
    echo " release   - release number ex. 1.10.1.0"
    echo " path      - root of magento sources directory"
    echo; echo "Example:"
    echo " `basename $0` upload ee 1.10.1.0 ./release/ee/1.10.1.0 "
    ;;
  esac
 fi
 
}

#################################################################################
# Create release
# Params:
#    version    ce or ee or pe
#    release    release to be created for ex. 1.10.1.0
#    path   	sources of code
#    svn	SVN link to get sources
#################################################################################
create() {
 mVersion="$2"
 if test "x$mVersion" = "x"; then
  echo -n "Enter Magento version [ee/pe/ce]: "
  read mVersion
  if [ -n "$mVersion" ] && [ "$mVersion" = "ee" ] || [ "$mVersion" = "pe" ] || [ "$mVersion" = "ce" ]; then
   echo "Version is $mVersion"
  else
   echo "Bad Magento Version"
   exit $E_BADARGS
  fi
 fi
 mVersion_txt=""
 case "$mVersion" in
    ce)
	    mVersion_txt=""
	    ;;
    ee)
	    mVersion_txt="Enterprise/"
	    ;;
    pe)
	    mVersion_txt="Professional/"
	    ;;
 esac


 mRelease="$3"
 if test "x$mRelease" = "x"; then
  echo -n "Enter Magento release number [Ex: 1.5.1.3]: "
  read mRelease
  if [ -n "$mRelease" ]; then
   echo "Release is $mRelease"
  else
   echo "Bad Magento release number"
   exit $E_BADARGS
  fi
 fi

 mSourcesDir="$4"
 if test "x$mSourcesDir" = "x"; then
  #echo -n "Enter Magento release source directory : "
  #read mSourcesDir
  mSourcesDir="$RELEASE_DIR/$mVersion/$mRelease"
 fi

 mSvn=""
 mSvnLink="$5"
 if [ "x$mSvnLink" != "x" ]; then
  mSvn="1"
 elif [[ "$mSourcesDir" == svn://* ]] || [[ "$mSourcesDir" == http://* ]]; then
  mSvn="1"
  mSvnLink="$mSourcesDir"
  mSourcesDir=$RELEASE_DIR/$mVersion/`basename $mSourcesDir`
 fi

 if [ ! -d "$mSourcesDir" ]; then
  mkdir -p $mSourcesDir
 fi

 # create mRelease folder in ./release/mVersion/ and checkout from svn then copy
 #  *.xml from trunk/var/connect/ latest release

 CD=`pwd`

 # get extensions data files *.xml
 #cd $RELEASE_DIR/trunk/var/connect
 # update repository
 #svn up -q --username $SVN_LOGIN --password "$SVN_PASSWORD"
 #cd $CD

 #prepare release sources folder
 if [ -n "$mSvn" ]; then
  #echo "svn"
  # clear dir if not empty
  if [ "$(ls -A $mSourcesDir)" ]; then
   rm -rf $mSourcesDir
   mkdir -p $mSourcesDir
  fi

  svn checkout -q --username $SVN_LOGIN --password "$SVN_PASSWORD" $mSvnLink $mSourcesDir
  cp $SOURCE_DIR/local/local_$mVersion.xml $mSourcesDir/app/etc/local.xml
 elif [ ! -f $mSourcesDir/app/etc/local.xml ]; then
  cp $SOURCE_DIR/local/local_$mVersion.xml $mSourcesDir/app/etc/local.xml
 fi

 if [ ! -d "$mSourcesDir/var/connect/$mRelease" ] || [ "$(ls -A $mSourcesDir/var/connect/$mRelease)" ]; then
  # copy extensions data files to the magento/var/connect directory
  mkdir -p $mSourcesDir/var/connect
  
  #cp -R $RELEASE_DIR/trunk/var/connect/$mVersion_txt$mRelease $mSourcesDir/var/connect
  svn export -q --username $SVN_LOGIN --password "$SVN_PASSWORD" $TRUNK_URL/var/connect/$mVersion_txt$mRelease \
   $mSourcesDir/var/connect
  
  
 fi

 # set writable directories
 cd $mSourcesDir
 chmod 777 ./app/etc
 chmod 777 ./media
 chmod 777 ./var

 find ./media -type f -exec chmod 666 {} \;&& find ./media -type d -exec chmod 777 {} \; 
 find ./var -type f -exec chmod 666 {} \;&& find ./var -type d -exec chmod 777 {} \; 
 cd $CD

 #create packages
 $SOURCE_DIR/package_all.php $mSourcesDir $mRelease
}

#################################################################################
# Upload release packages to channel server
# Params:
#    version    ce or ee or pe
#    release    release number ex. 1.10.1.0
#    path   	sources of code
#################################################################################
upload() {
 mVersion="$2"
 if test "x$mVersion" = "x"; then
  echo -n "Enter Magento version [ee/pe/ce]: "
  read mVersion
  if [ -n "$mVersion" ] && [ "$mVersion" = "ee" ] || [ "$mVersion" = "pe" ] || [ "$mVersion" = "ce" ]; then
   echo "Version is $mVersion"
  else
   echo "Bad Magento Version"
   exit $E_BADARGS
  fi
 fi

 mRelease="$3"
 if test "x$mRelease" = "x"; then
  echo -n "Enter Magento release number [Ex: 1.5.1.3]: "
  read mRelease
  if [ -n "$mRelease" ]; then
   echo "Release is $mRelease"
  else
   echo "Bad Magento release number"
   exit $E_BADARGS
  fi
 fi

 mSourcesDir="$4"
 if test "x$mSourcesDir" = "x"; then
  #echo -n "Enter Magento release source directory : "
  #read mSourcesDir
  mSourcesDir="$RELEASE_DIR/$mVersion/$mRelease"
 fi

 case "$mVersion" in
    ce)
	    mChannel="community"
	    ;;
    ee)
	    mChannel="enterprise"
	    ;;
    pe)
	    mChannel="professional"
	    ;;
 esac
 
 php -f $SOURCE_DIR/upload_packages.php $mSourcesDir/var/connect $mChannel test

}

rCommand="$1"
if [[ "$1" == \-* ]]; then
 rOptions="$1"
 shift
fi
case "$rOptions" in
    "--help")
       rCommand="help"
       ;;
    "-h")
       rCommand="help"
       ;;
esac

case "$rCommand" in
    help)
	    usage $@
	    ;;
    create)
	    create $@
	    ;;
    upload)
	    upload $@
	    ;;
esac
