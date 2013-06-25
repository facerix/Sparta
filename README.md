SPARTA -- A Tiny Blog Engine
---------------------------------------------

Sparta is a pet project project of mine, a minimalist blog/CMS engine written in PHP and MySQL.

There are many like it, [but this one is mine](http://en.wikipedia.org/wiki/Rifleman's_Creed/ "&ldquo;Seven-six-two millimeter. Full metal jacket.&rdquo;").

Its primary motivation is twofold:

1. [Get me off a hosted blog platform](http://palagpat-coding.blogspot.com/2010/09/reclaiming-content-manifesto.html), so I own my own content,
2. Give me a sandbox to play with cool new web development features.

Some effort has gone into providing an out-of-the-box style that's not hideous, and that has basic responsive design. So far, the RWD aspects really only manifest in the management pages, although I expect I'll get around to doing the blog reader pages as well when I responsivate (&trade;) my own blog.


INSTALLATION
------------

1. Get the [H2O](https://github.com/speedmax/h2o-php) template engine and drop it in a `bin/h2o` subfolder.
2. Create a new MySQL database on your server, and run the `sql/create_tables.sql` script to build its schema.
3. Copy `alibaba_config_sample.php` to `alibaba_config.php`, and edit it to reflect the database you just created.
4. Copy `sparta_config_sample.php` to `sparta_config.php`, and edit it to reflect the same database, as well as your desired blog information.


CREDITS
------------
I'm using [Ben Crowder's](http://bencrowder.net/) public domain 
[Alibaba](https://github.com/bencrowder/alibaba) project for its minimal PHP authentication. Those portions are his, and are quite probably more free of bugs than my own.