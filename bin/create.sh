#!/bin/bash

# number of expected arg to the script
# {1} schema name
# {2} mysql user
# {3} mysql user password
EXPECTED_ARGS=3
E_BADARGS=65

# path to mysql cli client
MYSQL="$(which mysql)";

# script execute directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# test for expected args
if [ $# -ne $EXPECTED_ARGS ]
then
  echo "Usage: $0 dbname dbuser dbpass";
  exit $E_BADARGS;
fi
 
# execute schema sql file 
echo 'Build Schema';
SCHEMA_FILES=(calendar timeslots rules schedule bookings)

for i in "${SCHEMA_FILES[@]}"
do
	echo "Processing Schema file $i.sql";
	rm /var/tmp/tmp.sql 2> /dev/null;
    touch /var/tmp/tmp.sql
    cat ${DIR}'/../database/schema/header.sql' >> /var/tmp/tmp.sql;
    cat ${DIR}'/../database/schema/'$i'.sql' >>   /var/tmp/tmp.sql;
    cat ${DIR}'/../database/schema/footer.sql' >> /var/tmp/tmp.sql;
    
	$MYSQL -u ${2} -p${3} ${1}  < /var/tmp/tmp.sql;
done



# execute every sql file under take packages directory 
echo 'Insert Packages';

for f in $(find ${DIR}/../database/packages/ -name '*.sql');
do
  echo "Processing file $f";
  $MYSQL -u ${2} -p${3} ${1} < $f;
done 
 
# execute common data file
echo 'Insert common data for install';
$MYSQL -u ${2} -p${3} ${1} < ${DIR}'/../database/data/common.sql';
 
