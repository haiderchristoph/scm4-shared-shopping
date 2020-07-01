# scm4-shared-shopping
FH Hagenberg SCM4 final project.

# What is this Shared Shopping?
It's a website where people, who are at health risk with the current Corona situation, can create shopping lists.
Those shopping lists also provide articles (title, amount, price_limit) and a date until it should be done.
Volunteers can then take over such a list and get the articles for the helpseekers.

# Used Technology
The application is based on PHP and HTML only.
The requirement for the project actually was to only use PHP in the Backend but also in the Frontend to add the necessary frontend logic.

# Getting Started
In ```etc/``` you can find the SQL file to build up the Database with the necessary tables, users and also dummy data.
The application was developed with XAMPP, so put the application folder to htdocs on your Apache server.
You may also provide the correct DB Connection credentials in ```lib/Data/DataManager.php``` before you start it.

Further information can be found in ```doc/```.

# Disclaimer
This is just a project made for my studies. It only uses PHP and procudes spaghetti code in the frontend.
The project should not be held as a good coding example, it's really not.
