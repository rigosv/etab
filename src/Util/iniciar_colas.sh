#!/bin/bash

PHP=`which php`
#CONSOLE="$PHP ../bin/console --env=prod"

cd "$( dirname "${BASH_SOURCE[0]}" )";

PATH="../../"

#CONSUMERS=`$CONSOLE MINSAL:queue:get_all_consumers`

#for CONSUMER in $CONSUMERS
#    do
/usr/bin/nohup $PHP $PATH/bin/console rabbitmq:consumer cargar_origen_datos > $PATH"/var/log/rabbitmq_cargar_origen_datos.log" &
/usr/bin/nohup $PHP $PATH/bin/console rabbitmq:consumer guardar_registro > $PATH"/var/log/rabbitmq_guardar_registro.log" &
#done

