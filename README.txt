This is the README file for the MooTyper project. MooTyper is
a moodle extension, that adds the 'typing instructor' functionallity to Moodle. 
The plugin url is: https://moodle.org/plugins/view.php?plugin=mod_mootyper

MooTyper is a free software just like Moodle.

1. REQUIREMENTS

Module uses javascript which is not welcome in Moodle but it is a requirement
for the use of the module. The typing procedure cannot be done without the
client side logic.

It creates some new tables in a moodle database and inserts some sample
typing exercises. This is all done automaticaly through the intstallation,
but real lessons and exercises should be added by teachers afterwards.

2. INSTALLATION

MooTyper is an activity module. You have to extract it to the 'mod' directory.
If directory name is something like "moodle_mod_mootyper" you have to change
that to just "mootyper". So for example path should be like this:
<your moodle installation>/mod/mootyper/view.php

Than go to Site Administration -> Notifications and click on the button below.

3. USING MOOTYPER

Using MooTyper activity module is very simple. An instance can be added as
new activity in a course like Lesson or Quiz. Thanks to Mary Cooch from moodle.org
we have this video, which shows how to add exercises, create mootyper instance,
and than view grades. It's a little outdated (one of the first versions of
mootyper), but I guess everything stil holds:

http://www.youtube.com/watch?v=Twl-7CGrS0g

4. ADDITIONAL KEYBOARD LAYOUTS

Module currently includes support for English and Slovenian keyboard layout.

To implement any other layout you have to:
Create a php file with keyboard layout defined with HTML. Create javascript
file(with the same name and .js extension) that defines some functions for
your layout. If you have any mistakes in your js file the module won't work,
so in this case try to validate your code with a tool like this...
http://www.javascriptlint.com/online_lint.php

For more info please visit the plugins wiki on github:
https://github.com/jl2035/moodle_mod_mootyper/wiki

This plugin is being maintained for 3 years now, by only one person, so
donations are welcome: 1EHY4bD2hwfeygfVCoah1JzVWgPNyHfk6s
