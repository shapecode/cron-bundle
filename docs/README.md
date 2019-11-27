Shapecode - Cron Bundle
=======================

[![paypal](https://img.shields.io/badge/Donate-Paypal-blue.svg)](http://paypal.me/nloges)

[![PHP Version](https://img.shields.io/packagist/php-v/shapecode/cron-bundle.svg)](https://packagist.org/packages/shapecode/cron-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/shapecode/cron-bundle.svg?label=stable)](https://packagist.org/packages/shapecode/cron-bundle)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/shapecode/cron-bundle.svg?label=unstable)](https://packagist.org/packages/shapecode/cron-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/shapecode/cron-bundle.svg)](https://packagist.org/packages/shapecode/cron-bundle)
[![Monthly Downloads](https://img.shields.io/packagist/dm/shapecode/cron-bundle.svg)](https://packagist.org/packages/shapecode/cron-bundle)
[![Daily Downloads](https://img.shields.io/packagist/dd/shapecode/cron-bundle.svg)](https://packagist.org/packages/shapecode/cron-bundle)
[![License](https://img.shields.io/packagist/l/shapecode/cron-bundle.svg)](https://packagist.org/packages/shapecode/cron-bundle)


This bundle provides a simple interface for registering repeated scheduled
tasks within your application.

Install instructions
--------------------------------

Installing this bundle can be done through these simple steps:

Add the bundle to your project through composer:
```bash
composer require shapecode/cron-bundle
```

Add the bundle to your config if it flex did not do it for you:
```php
<?php

// config/bundles.php
return [
    // ...
    Shapecode\Bundle\CronBundle\ShapecodeCronBundle::class,
    // ...
];
```

Update your DB schema ...

... with Doctrine schema update method ...
```bash
php bin/console doctrine:schema:update --force
```

Creating your own tasks
--------------------------------

Creating your own tasks with CronBundle couldn't be easier - all you have to do is create a normal Symfony2 Command (or ContainerAwareCommand) and tag it with the CronJob annotation, as demonstrated below:

```php
<?php

declare(strict_types=1);

namespace App\DemoBundle\Command;

use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CronJob("*\/5 * * * *")
 * Will be executed every 5 minutes
 */
class DemoCommand extends Command
{
    
    public function configure() : void
    {
		// Must have a name configured
		// ...
    }
    
    public function execute(InputInterface $input, OutputInterface $output) : void
    {
		// Your code here
    }
}
```

The interval spec ("*\/5 * * * *" in the above example) use the standard cronjob schedule format and can be modified whenever you choose. You have to escape the / in this example because it would close the annotation.
You can also register your command multiple times by using the annotation more than once with different values.
For your CronJob to be scanned and included in future runs, you must first run `php bin/console shapecode:cron:scan` - it will be scheduled to run the next time you run `php app/console shapecode:cron:run`

Register your new Crons:
```bash
$ php bin/console shapecode:cron:scan
$ php bin/console shapecode:cron:run
```

Running your cron jobs automatically
--------------------------------

This bundle is designed around the idea that your tasks will be run with a minimum interval - the tasks will be run no more frequently than you schedule them, but they can only run when you trigger then (by running `bin/console shapecode:cron:run`).

To facilitate this, you can create a cron job on your system like this:
```bash
*/5 * * * * php /path/to/symfony/bin/console shapecode:cron:run
```
This will schedule your tasks to run at most every 5 minutes - for instance, tasks which are scheduled to run every 3 minutes will only run every 5 minutes.
