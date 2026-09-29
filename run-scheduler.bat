@echo off
cd /d C:\Users\HomePC\Desktop\smart-health-advisor
echo ===== %date% %time% ===== >> storage\logs\scheduler.log
C:\php\php.exe artisan schedule:run >> storage\logs\scheduler.log 2>&1