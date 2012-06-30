This is the README file for the MooTyper project. MooTyper is a moodle extension, 
that simulates the 'typing instructor' programs. MooTyper is a free software just
like Moodle.

1. REQUIREMENTS

Module uses javascript which is not welcome in Moodle but it is a requirement for 
the use of the module. It creates some new tables in a moodle database and inserts a 
quite some sample typing exercises. This is all done automaticaly through the intstallation.
After the successfull installation you will have a complete collection of exercises,
but you can also add your owns and modify or delete the existing ones. 

2. INSTALLATION

MooTyper is an activity module. You have to extract it to the 'mod' directory. Than
go to Site Administration -> Notifications and click on the button below.

3. USE

Using MooTyper activity module is very simple. An instance can be added as new activity in a
course like Lesson or Quiz. This video shows how to add exercise, create an instance
and than view grades: 
[Video was removed due to futher development and changes in the module]

4. ADDITIONAL KEYBOARD LAYOUTS

Module currently includes support for English and Slovenian keyboard layout. If anybody wants
to implement any other keyboard please contact me at http://github.com/jl2035

What needs to be done: Create a php file with keyboard layout defined with HTML. Create
javascript file(with the same name and .js extension) that overrides some functions for
your layout. If you have any mistakes in your js file the module won't work, so in this
case try to validate your code with a tool like this: http://www.javascriptlint.com/online_lint.php

Good luck!
