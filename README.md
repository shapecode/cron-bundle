Shapecode Cron Bundle
=======================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cd190858-da13-4be6-ad02-c933d4272d87/mini.png)](https://insight.sensiolabs.com/projects/cd190858-da13-4be6-ad02-c933d4272d87)
[![Dependency Status](https://www.versioneye.com/user/projects/57703c8c671894004e1a9103/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57703c8c671894004e1a9103)
[![Latest Stable Version](https://poser.pugx.org/shapecode/cron-bundle/v/stable)](https://packagist.org/packages/shapecode/cron-bundle)
[![Total Downloads](https://poser.pugx.org/shapecode/cron-bundle/downloads)](https://packagist.org/packages/shapecode/cron-bundle)
[![Latest Unstable Version](https://poser.pugx.org/shapecode/cron-bundle/v/unstable)](https://packagist.org/packages/shapecode/cron-bundle)
[![License](https://poser.pugx.org/shapecode/cron-bundle/license)](https://packagist.org/packages/shapecode/cron-bundle)

This bundle provides a simple interface for registering repeated scheduled
tasks within your application.

Install instructions
--------------------------------

Installing this bundle can be done through these simple steps:

Add the bundle to your project as a composer dependency:
```javascript
// composer.json
{
    // ...
    require: {
        // ...
        "shapecode/cron-bundle": "~1.3"
    }
}
```

Update your composer installation:
```sh
$ composer update --prefer-dist
```

Add the bundle to your application kernel:
```php
<?php

// application/ApplicationKernel.php
public function registerBundles()
{
	// ...
	$bundle = array(
		// ...
        new Shapecode\Bundle\CronBundle\ShapecodeCronBundle(),
	);
    // ...

    return $bundles;
}
```

Update your DB schema ...

... with Doctrine standard method ...
```sh
$ php app/console doctrine:schema:update --force
```

Creating your own tasks
--------------------------------

Creating your own tasks with CronBundle couldn't be easier - all you have to do is create a normal Symfony2 Command (or ContainerAwareCommand) and tag it with the CronJob annotation, as demonstrated below:

```php
<?php

namespace App\DemoBundle\Command;

use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
// use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class DemoCommand
 * @package App\DemoBundle\Command
 * @author Nikita Loges
 *
 * @CronJob("PT1H")
 * Will be executed every hour
 */
class DemoCommand extends Command
{
    public function configure()
    {
		// Must have a name configured
		// ...
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
		// Your code here
    }
}
```

The interval spec ("PT1H" in the above example) is documented on the [DateInterval](http://php.net/manual/en/dateinterval.construct.php) documentation page, and can be modified whenever you choose.
For your CronJob to be scanned and included in future runs, you must first run `php app/console cron:scan` - it will be scheduled to run the next time you run `php app/console cron:run`

Register your new Crons:
```sh
$ php app/console cron:scan
$ php app/console cron:run
```

Running your cron jobs automatically
--------------------------------

This bundle is designed around the idea that your tasks will be run with a minimum interval - the tasks will be run no more frequently than you schedule them, but they can only run when you trigger then (by running `app/console cron:run`).

To facilitate this, you can create a cron job on your system like this:
```
*/5 * * * * php /path/to/symfony/app/console cron:run
```
This will schedule your tasks to run at most every 5 minutes - for instance, tasks which are scheduled to run every 3 minutes will only run every 5 minutes.

