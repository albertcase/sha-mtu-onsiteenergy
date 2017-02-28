#!/bin/bash
result=$(ps -aux|grep customsMsgSend.php|grep -v grep)
if [[ $result ]]
then
	echo 'this service already runing';
	exit;
fi

folder=$( cd "$( dirname "$0"  )" && pwd  );
php $folder"/customsMsgSend.php" >> $folder"/customsMsgSend.log"  2>&1 &
