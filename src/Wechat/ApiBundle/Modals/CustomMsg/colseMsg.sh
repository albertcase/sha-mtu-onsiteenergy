#!/bin/bash
result=$(ps -aux|grep customsMsgSend.php|grep -v grep);
if [[ $result ]]
then
  psid=${result:8:7};
  kill -9 $psid;
  exit;
fi
exit;
