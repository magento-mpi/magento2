#!/bin/bash
# location of the php binary
OLDPWD=`pwd`
PHP_BIN="/usr/bin/php"
BUILD_NAME="$1"
CHECKOUT_DIR="$2"
PWD="/opt/builds/$1/"
DB_PRE_NAME="builds_$1_$2"
DB_NAME=${DB_PRE_NAME//-/_}
NUM_BUILDS=10
OLDIFS=$IFS
SCRIPTS_PATH="/opt/builds/script/"
LOGNAME="$SCRIPTS_PATH/build.log"

failure() {
    log "ERROR"
    IFS=$OLDIFS
    cd $OLDPWD
    
    exit 1
}

success() {
    log "OK"
}

log() {
    echo "`date "+%Y-%m-%d %H:%M:%S"`: $1" | tee -a $LOGNAME
}

testsite() {
    log "Testing site"
    $SCRIPTS_PATH/pavuk.sh "$BUILD_NAME/$CHECKOUT_DIR"
    [ ! "$?" -eq 0 ] && failure || success
    log "Lookidy and ng for errors in $CHECKOUT_DIR/var/report/"
    if [ -d $CHECKOUT_DIR/var/report/ ] ; then
        find $CHECKOUT_DIR/var/report/ -type f && failure || success
    else 
        success    
    fi
}

ch_baseurl() {
    log "Changing unsecure baseurl to $BUILD_NAME/$1"
    echo "UPDATE $2.prefix_core_config_data SET value = 'http://kq.varien.com/builds/$BUILD_NAME/$1/' WHERE path like 'web/unsecure/base_url';" | mysql -u root
    [ ! "$?" -eq 0 ] && failure || success
    log "Changing secure baseurl to $BUILD_NAME/$1"
    echo "UPDATE $2.prefix_core_config_data SET value = 'https://kq.varien.com/builds/$BUILD_NAME/$1/' WHERE path like 'web/secure/base_url';" | mysql -u root 
    [ ! "$?" -eq 0 ] && failure || success
}

clean_cache() {
    log "Cleaning cache for $1";
    rm -rf $1/var/cache/*
    [ ! "$?" -eq 0 ] && failure || success
}

log "Script start"

log "Drop DB $DB_NAME"
echo "DROP DATABASE IF EXISTS $DB_NAME;" | mysql -u root
[ ! "$?" -eq 0 ] && failure || success

log "Create DB..."
echo "CREATE DATABASE $DB_NAME;" | mysql -u root
[ ! "$?" -eq 0 ] && failure || success

log "cd to: $PWD"
cd $PWD 
[ ! "$?" -eq 0 ] && failure || success

log "Copying local.xml.template"
cp -f "$SCRIPTS_PATH/local.xml.template" "$CHECKOUT_DIR/app/etc/local.xml.template"
[ ! "$?" -eq 0 ] && failure || success

log "Copying XEnterprise_Enabler.xml"
cp -f "$SCRIPTS_PATH/XEnterprise_Enabler.xml" "$CHECKOUT_DIR/app/etc/modules/"
[ ! "$?" -eq 0 ] && failure || success

log "Installing build..."
$PHP_BIN -f $CHECKOUT_DIR/install.php -- --license_agreement_accepted yes \
--locale en_US --timezone "America/Los_Angeles" --default_currency USD \
--db_host "127.0.0.1:3306" --db_name "$DB_NAME"  --db_user "qa_setup" --db_pass "qa_setup" \
--db_prefix "prefix_" \
--use_rewrites yes \
--admin_frontname "control" \
--skip_url_validation yes \
--url "http://kq.varien.com/builds/$BUILD_NAME/$CHECKOUT_DIR/" \
--secure_base_url "https://kq.varien.com/builds/$BUILD_NAME/$CHECKOUT_DIR/" \
--use_secure yes --use_secure_admin yes \
--admin_lastname "Vasilenko" --admin_firstname "Dmitriy" --admin_email "dimav@varien.com" \
--admin_username "admin" --admin_password "123123q" \
--encryption_key "mega1nightly1test1build" 
[ ! "$?" -eq 0 ] && failure || success

testsite

log "Archiving old builds"
IFS=$'\n'
for file in `find * -maxdepth 0 -type d`
do
    if ! find * -maxdepth 0 -type d | tail -n $NUM_BUILDS | grep "$file" > /dev/null ; then
        log "Archiving $file" 
        if [ ! -d ../archive/$BUILD_NAME ] ; then
    	    log "Creating ../archive/$BUILD_NAME"
	    mkdir ../archive/$BUILD_NAME
	    [ ! "$?" -eq 0 ] && failure || success
	fi
	tar -czf ../archive/$BUILD_NAME/"$file".tgz $file
	[ ! "$?" -eq 0 ] && failure || success

	DB_ARCH_PRE_NAME="builds-$1_$file"
	echo $DB_ARCH_PRE_NAME
	DB_ARCH_NAME=${DB_ARCH_PRE_NAME//-/_}
	log "Archiving DB $DB_ARCH_NAME"		
        if [ ! -d ../archive/$BUILD_NAME/sql ] ; then
    	    log "Creating ../archive/$BUILD_NAME/sql"
	    mkdir ../archive/$BUILD_NAME/sql
	    [ ! "$?" -eq 0 ] && failure || success
	fi
	mysqldump -u root $DB_ARCH_NAME | gzip > ../archive/$BUILD_NAME/sql/$DB_ARCH_NAME.sql.gz
	[ ! "$?" -eq 0 ] && failure || success	
	
        log "Deleting $file" 	
	rm -rf $file
	[ ! "$?" -eq 0 ] && failure || success	
	
        log "Dropping DB $DB_ARCH_NAME"
	echo "DROP DATABASE IF EXISTS $DB_ARCH_NAME;" | mysql -u root                                                                                                                                                                                                     
	[ ! "$?" -eq 0 ] && failure || success	
	
    fi
done

log "Looking last successful build"
SUCC_BUILD=`cat ../script/successful_builds/$BUILD_NAME`
[ ! "$?" -eq 0 ] && failure || success	

log "Copying $SUCC_BUILD/websites"
mkdir "$CHECKOUT_DIR/websites/" && cp -a "$SUCC_BUILD/websites" "$CHECKOUT_DIR" 
[ ! "$?" -eq 0 ] && failure || success	

log "Changing website permissions"
chmod -R 777 "$CHECKOUT_DIR/websites"
[ ! "$?" -eq 0 ] && failure || success

if [ ! -d "$CHECKOUT_DIR/media/" ] ; then 
    mkdir "$CHECKOUT_DIR/media/" 
fi

log "Changind media permissions"
chmod -R 777 "$CHECKOUT_DIR/media/"
[ ! "$?" -eq 0 ] && failure || success	
log "Copying $SUCC_BUILD/media"
cp -a "$SUCC_BUILD/media" "$CHECKOUT_DIR" 
[ ! "$?" -eq 0 ] && failure || success	

log "Changind media permissions"
chmod -R 777 "$CHECKOUT_DIR/media/"
[ ! "$?" -eq 0 ] && failure || success	

log "Changing var permissions"
chmod -R 777 "$CHECKOUT_DIR/var/"
[ ! "$?" -eq 0 ] && failure || success	

DB_CONV_TMP="builds-$BUILD_NAME-$SUCC_BUILD"
DB_CONV=${DB_CONV_TMP//-/_}
log "Looking DB for $DB_CONV"
echo 'SHOW DATABASES;' | mysql -u root | grep $DB_CONV > /dev/null
    if [ "$?" -eq 0 ] ; then
        log "DB Found"
	    
        log "Drop DB $DB_NAME"
        echo "DROP DATABASE IF EXISTS $DB_NAME;" | mysql -u root
        [ ! "$?" -eq 0 ] && failure || success

        log "Create DB..."
        echo "CREATE DATABASE $DB_NAME;" | mysql -u root
        [ ! "$?" -eq 0 ] && failure || success
	    
        log "Copying DB $DB_CONV to $DB_NAME"
        mysqldump -u root $DB_CONV | mysql -u root $DB_NAME
	else
	    failure
	fi

ch_baseurl $SUCC_BUILD $DB_CONV

clean_cache $SUCC_BUILD

ch_baseurl $CHECKOUT_DIR $DB_NAME

clean_cache $CHECKOUT_DIR

testsite

ch_baseurl "current" $DB_NAME

clean_cache $CHECKOUT_DIR

log "Updating 'current' link..."
if [ -L "current" ]; then 
    rm current 
    [ ! "$?" -eq 0 ] && failure || success    
fi
ln -sf $CHECKOUT_DIR current
[ ! "$?" -eq 0 ] && failure || success

log "Writing successful build info"
echo "$CHECKOUT_DIR" > ../script/successful_builds/"$BUILD_NAME"
[ ! "$?" -eq 0 ] && failure || success    
    
IFS=$OLDIFS
cd $OLDPWD
