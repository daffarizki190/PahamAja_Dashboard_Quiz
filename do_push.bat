@echo off
cd /d "c:\Users\MyBook Hype AMD\OneDrive\Documents\VS Code Semester 3\Dashboard_Quis"
echo --- Git Initialization --- > push_log.txt
git init >> push_log.txt 2>&1
echo --- Git Add --- >> push_log.txt 2>&1
git add . >> push_log.txt 2>&1
echo --- Git Commit --- >> push_log.txt 2>&1
git commit -m "Initial push of used code" >> push_log.txt 2>&1
echo --- Git Remote --- >> push_log.txt 2>&1
git remote add origin https://github.com/daffarizki190/PahamAja_Dashboard_Quiz.git >> push_log.txt 2>&1
echo --- Git Push --- >> push_log.txt 2>&1
git push -u origin master >> push_log.txt 2>&1
echo --- Done --- >> push_log.txt 2>&1
