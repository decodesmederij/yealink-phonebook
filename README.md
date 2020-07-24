Octivi - Yealink PhoneBook Manager
=============================================

Simple PHP application to manage your Yealink Remote phone book.

Check out the [post about the release](http://labs.octivi.com/yealink-phone-book-manager-released/) on our blog!

Installation
---------------------------------------------

### Using Composer

Under your host directory clone this Git repository.

1. In project folder execute commands:

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install

2. Set the server's document root to the public/ folder


.htaccess password protection
---------------------------------------------

It's possible to configure a .htaccess file to ensure Basic access authentication protection. All you have to do is to uncomment
last comment block in the .htaccess file, and set the right path of your .htpasswd file.

    AuthUserFile /path/to/your/directory/.htpasswd

Yealink phones still can't get access through basic auth, so to allow them to read your phone book files, you must edit line:

    Allow from 127.0.0.1

where 127.0.0.1 has to be your phone IP address.


Phonebook backup
---------------------------------------------

In the same directory as your orginal phonebook file is, the script will create backup files of your phonebook when clicking on the "Create Backup" button.


Copyright
---------------------------------------------

Copyright 2014 IMAGIN Sp. z o.o.
Octivi - www.octivi.com
