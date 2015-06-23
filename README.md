Plow
====


Description
----

Plow is designed to be your new best friend when developing PHP application.
At its core, it's just a plugin infrastructure for various command-line tools.


Installation
----
Plow should be globally installed with Composer.
Follow [these instructions](https://getcomposer.org/doc/00-intro.md#globally) to install Composer if you don't have it yet.

    composer global require firehed/plow

If you have not done so already, you should append Composer's global bin directory to your path:

    echo "export PATH=\$PATH:~/.composer/vendor/bin" >> ~/.bash_profile
    source ~/.bash_profile

Usage
----

Running `plow` with no arguments on the command line will provide detailed usage instrictions:


    plow


Adding commands
----

Plow commands are Composer packages with the type specified as `plow-command`.
While not directly shown in the UI, you can [search Packagist by type](https://packagist.org/search/?type=plow-command)
Once you find what you're looking for, install it globally like any other package:

    composer global require vendor-name/plow-command-package-name


Developing new commands
----

See [CREATING_COMMANDS.md](CREATING_COMMANDS.md)

Contributing to Plow
----

See [CONTRIBUTING.md](CONTRIBUTING.md)
