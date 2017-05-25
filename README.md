# Students
Educational project: you can **browse** list of students, use **search**, add **your** own data, which will give you cookie token, so you could **edit** your 'Student profile' later.

We use mysql/MariaDB with two tables: one holding data about **profiles**, another manipulates **password** hashes.

Now I will say a few words about Project's structure:
|-Students/ (main folder, lies at of the site, i.e. htdocs/s1.localhost/Students)
----index.php     : first entry point. From here user can view the table, search, change app mode (per user), etc.
----reg-edit.php  : second entry point. From here user can register (insert his data) or change info in his profile.
----bootstrap.php : contains basic initialization for both entry scripts.
----errors.log    : here scripts write info about errors if in 'production' mode.
----About modes: there are two of them, indicating how much info about errors user can get. 
-----If 'production', user gets only fact of error, if 'development' - details of error, including filepaths and       ------structure.
----ini/ (contains configuration files) (if you deploy database, be sure to edit your name/pass here)
----Shinoa/ (basic folder for class files like in psr-1) ('vendor' namespace)
------ StudentList/ (project name) 
---------SearchQueryValidator, StudentValidator.php : they check input data and always produce correct/secure values.
---------StudentMapper, PasswordMapper : interaction with database tables. Need to get SQL somewhere.
---------StudentSQLBuilder, PassSQLBuilder.php : (semi)dynamically produces SQL for various tasks.
---------LoginManager : logs and logs out users, checks their credeintials, using cookies.
---------CommonView, StudentView, RegEditView.php : displays result to user.
---------StatusSelector : Manages user's choice of app mode.
---------ErrHelper, ErrEvoker.php : works with errors and exceptions, also displays them instead of ...View.php
----------Be ware, that bootstrap.php also contains error handling in case everything in scripts fails.
-------Registry : stores data for communication between different layers of app. Replaces part of global variables, ----------provides interface for them.
       



