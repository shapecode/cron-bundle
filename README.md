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
        "shapecode/cron-bundle": "~2.0"
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
	$bundles = array(
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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
// use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class DemoCommand
 * @package App\DemoBundle\Command
 * @author Nikita Loges
 *
 * @CronJob("*\/5 * * * *")
 * Will be executed every 5 minutes
 */
class DemoCommand extends Command
{
    
    /**
     * @inheritdoc
     */
    public function configure()
    {
		// Must have a name configured
		// ...
    }
    
    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
		// Your code here
    }
}
```

The interval spec ("*\/5 * * * *" in the above example) use the standard cronjob schedule format and can be modified whenever you choose. You have to escape the / in this example because it would close the annotation.
You can also register your command multiple times by using the annotation more than once with different values.
For your CronJob to be scanned and included in future runs, you must first run `php app/console shapecode:cron:scan` - it will be scheduled to run the next time you run `php app/console schapede:cron:run`

Register your new Crons:
```sh
$ php app/console schapecode:cron:scan
$ php app/console schapecode:cron:run
```

Running your cron jobs automatically
--------------------------------

This bundle is designed around the idea that your tasks will be run with a minimum interval - the tasks will be run no more frequently than you schedule them, but they can only run when you trigger then (by running `app/console cron:run`).

To facilitate this, you can create a cron job on your system like this:
```sh
*/5 * * * * php /path/to/symfony/app/console cron:run
```
This will schedule your tasks to run at most every 5 minutes - for instance, tasks which are scheduled to run every 3 minutes will only run every 5 minutes.
