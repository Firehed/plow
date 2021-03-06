# Creating commands

Plow is designed from the ground up to be very extensible, so adding new commands is straightforward.

## Implement `Firehed\Plow\CommandInterface`
The class(es) your package provides implementing this interface will actually do the work.
Plow itself handles common functionality such as CLI argument parsing, help screens, verbosity, etc.
See the [class docblocks](https://github.com/Firehed/plow/blob/master/src/CommandInterface.php) for details on how to do this.

**Tip!** Plow core also includes `Firehed\Plow\CommandTrait` which implements some sane defaults for you.
`use`ing it leaves you to provide only the command name, description, arguments, and execution; you can skip common meta-code like receiving argument values and the console output object.

### Events
While implementing the interface is enough to build a command successfully, it may be helpful to know the order in which the methods are called.

During installation (e.g. `composer require example/some-command`):

* `getCommandName()`
* `getAliases()`

During setup (argument validation, etc.):

* `getBanner()`
* `getOptions()`
* `getDescription()` if `-h`/`--help` is used
* `getSynopsis()` during `plow --list`
* `getCommandName()` and `getVersion()` of `-V/--version` is used

During execution:

* `setOutput()`
* `setOperands()`
* `setOptionValues()`
* `execute()`


## Configure `composer.json`
These sections all refer to top-level keys in `composer.json`.

### `require`
Add `firehed/plow` at version `^1.0.0`

### `type`
Set `type` to `plow-command`.
This will cause Composer to hand off the command to Plow during installation.
This is not present by default, so you may have to add it.

### `extra`
Set the `extra` to a dictionary including the key `plow`.
`plow` should be an array of your classes implementing `Firehed\Plow\CommandInterface`.
This is what Plow uses to determine what classes to register during installation.

### `autoload`
You may follow any autoloading system that Composer supports (PSR-4 is strongly recommended); the only requirement is that any classes named in `extra` must be autoloadable.

## Optional: register at Packagist

If you are making a new Plow command for wider distrubution, you should register it at [Packagist](https://packagist.org).
If it is for personal or internal use, this is not necessary, but you will probably have to specify its location in `repositories`.

## Sample `composer.json`
This is the bare minimum you can include in your Composer manifest to have your class(es) register with Plow during installation.

```
{
    "name": "example/some-command",
    "type": "plow-command",
    "require": {
        "firehed/plow": "^1.0.0"
    },
    "autoload": {
        "psr-4": {
            "Example\\SomeCommand\\": [
                "src"
            ]
        }
    },
    "extra": {
        "plow": [
            "Example\\SomeCommand\\CommandClass"
        ]
    }
}

```
