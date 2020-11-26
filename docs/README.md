# Shapecode - Cron Bundle

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

## Install instructions

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

## Creating your own tasks

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

## Running your cron jobs automatically

This bundle is designed around the idea that your tasks will be run with a minimum interval - the tasks will be run no more frequently than you schedule them, but they can only run when you trigger then (by running `bin/console shapecode:cron:run`).

To facilitate this, you can create a cron job on your system like this:
```bash
*/5 * * * * php /path/to/symfony/bin/console shapecode:cron:run
```
This will schedule your tasks to run at most every 5 minutes - for instance, tasks which are scheduled to run every 3 minutes will only run every 5 minutes.

## Disabling and enabling individual cron jobs from the command line

This bundle allows you to easily disable and enable individual scheduled CronJobs from the command-line.

To <strong>disable</strong> a CronJob, run: `bin/console shapecode:cron:edit your:cron:job --enable n`, where `your:cron:job` is the name of the CronJob in your project you would like to disable.

Running the above will disable your CronJob until you manually enable it again. Please note that even though the `next_run` field on the `cron_job` table will still hold a datetime value, your disabled cronjob will not be run.

To <strong>enable</strong> a cron job, run: `bin/console shapecode:cron:edit your:cron:job --enable y`, where `your:cron:job` is the name of the CronJob in your project you would like to enable.

## Config

### Clean Up

By default your logs will be cleared after 7 days to avoid to many entries in database.  
You can change this by setting configs. 

By default, all cronjobs run until they are finished (or exceed the [default timeout of 60s set by the Process component](https://symfony.com/doc/current/components/process.html#process-timeout). When running cronjob from a controller, a timeout for running cronjobs 
can be useful as the HTTP request might get killed by PHP due to a maximum execution limit. By specifying a timeout,
all jobs get killed automatically and the correct job result (which would not indicate any success) will be persisted
(see [#26](https://github.com/shapecode/cron-bundle/issues/26#issuecomment-731738093)) A default value of `null` specifies 
no timeout, otherwise you can specify the timeout in seconds (as `float`). See [Process component docs](https://symfony.com/doc/current/components/process.html#process-timeout).
**Important:** The timeout is applied to every cronjob, regardsless from where (controller or CLI) it is executed.

```yaml
shapecode_cron:
    timeout: null # default. A number (of type float) can be specified
    results:
        auto_prune: true # default
        interval: 7 days ago # default. A date time interval specification
```
