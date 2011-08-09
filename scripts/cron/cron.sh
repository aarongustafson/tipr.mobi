#!/bin/sh
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessInbox.php &      # runs at time :00
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessOutbox.php &
wget -q -O /dev/null http://tipr.mobi/scripts/cron/MakeFriends.php &
wget -q -O /dev/null http://tipr.mobi/scripts/cron/GetMessages.php &
sleep 10
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessInbox.php &      # runs at time :10
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessOutbox.php &
sleep 10
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessInbox.php &      # runs at time :20
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessOutbox.php &
sleep 10
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessInbox.php &      # runs at time :30
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessOutbox.php &
wget -q -O /dev/null http://tipr.mobi/scripts/cron/MakeFriends.php &
sleep 10
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessInbox.php &      # runs at time :40
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessOutbox.php &
sleep 10
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessInbox.php &      # runs at time :50
wget -q -O /dev/null http://tipr.mobi/scripts/cron/ProcessOutbox.php &
# CLEANUP
#rm -f /ProcessInbox.* &
#rm -f /ProcessOutbox.* &
#rm -f /MakeFriends.* &
#rm -f /GetMessages.* &
