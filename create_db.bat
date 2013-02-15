set/p db_name=Database Name: 

mysql -u root -p -e "create database %db_name%"