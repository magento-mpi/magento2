#!/bin/bash

. include.sh

cd $PWD

log "Archiving old builds..."
IFS=$'\n'
for file in `find * -maxdepth 0 -type d`
do
    if ! find * -maxdepth 0 -type d | tail -n $NUM_BUILDS | grep "$file" > /dev/null ; then
        log "Archiving $file" 
        if [ ! -d ../archive/$BUILD_NAME ] ; then
    	    log "Creating ../archive/$BUILD_NAME"
	    mkdir ../archive/$BUILD_NAME
	    [ ! "$?" -eq 0 ] && failure
	fi
	tar -czf ../archive/$BUILD_NAME/"$file".tgz $file
	[ ! "$?" -eq 0 ] && failure

	DB_ARCH_PRE_NAME="builds-$1_$file"
	echo $DB_ARCH_PRE_NAME
	DB_ARCH_NAME=${DB_ARCH_PRE_NAME//-/_}
	log "Archiving DB $DB_ARCH_NAME"		
        if [ ! -d ../archive/$BUILD_NAME/sql ] ; then
    	    log "Creating ../archive/$BUILD_NAME/sql"
	    mkdir ../archive/$BUILD_NAME/sql
	    [ ! "$?" -eq 0 ] && failure
	fi
	mysqldump -u root $DB_ARCH_NAME | gzip > ../archive/$BUILD_NAME/sql/$DB_ARCH_NAME.sql.gz
	[ ! "$?" -eq 0 ] && failure	
	
        log "Deleting $file" 	
	rm -rf $file
	[ ! "$?" -eq 0 ] && failure	
	
        log "Dropping DB $DB_ARCH_NAME"
	echo "DROP DATABASE IF EXISTS $DB_ARCH_NAME;" | mysql -u root                                                                                                                                                                                                     
	[ ! "$?" -eq 0 ] && failure	
    else 
	log "Nothing to archive."	
    fi
done

IFS=$OLDIFS
cd $OLDPWD
