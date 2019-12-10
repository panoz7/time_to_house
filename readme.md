Set up the CRON job: 

Run `crontab -e`
Add this line `0,30 * * * * path_to_php path_to_folder/getRouteTimes.php >> path_to_log/file.log`
Save it out