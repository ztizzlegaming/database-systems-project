# Database Systems Project

## James Easton, Jenna Graves, Zeyang Huang, Jordan Turley

### Overview

Final project for CSC 410: Database Systems at Centre College.

Previously, our client used a conglomeration of systems to keep track of their data, including Excel spreadsheets, MySQL and PostgreSQL databases, and other systems. Users did not know where to look for certain data, and it was hard to ensure all data was synchronized. If a person forgot to update data in one place, it could throw the entire system off.

My team and I developed a centralized system for our client as part of our database systems class. The client has a vast inventory of equipment that needs to be logged. Also, the client ships a lot of equipment around the world and needs to keep track of where it is at a certain time and if it has arrived at its destination. The client itself has many clients that it deals with that need to be stored. Finally, the client needed to protect this data, so a login system was required.

We developed a PostgreSQL database schema that can handle all of this. The database eliminates redundant data as much as possible, ensures data integrity through primary and foreign key rules, deny rules on deletions, and constraints like non-negative numbers or project end dates being later than the start dates. We also developed a PHP web app that allows the client to easliy interact with the database.

### Setup

We hosted our project on a Google Cloud Platform virtual machine, with the LAPP (Linux, Apache, PostgreSQL, PHP) stack, but it can be hosted on any cloud provider or on an individual's own system.

First, clone the repository:

    git clone https://github.com/ztizzlegaming/database-systems-project

The database setup script is at [DatabaseScripts/TM_database_creation.sql](https://github.com/ztizzlegaming/database-systems-project/blob/master/DatabaseScripts/TM_database_creation.sql). First, create a new database, then run the create database script. From the PostgreSQL console, type:

    \c tmdatabase
    \i TM_database_creation.sql

The PHP scripts for the project are located in `PHP_Pages/` and can simply be dropped in the `/var/www/html` folder of a virtual machine, after Apache and PHP have been setup.
