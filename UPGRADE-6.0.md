UPGRADE FROM 5.x to 6.0
=======================

Genereal
-----

* Many internal changes and use of Symfony 5.4 features.
* Use of PHP8.1 feature set

Annotations/Attributes
-----

* Removed `CronJob` Annotation, use `AsCronJob` Attribute instead.
  Before:
   ```php
    <?php
    
    // ....
    use Shapecode\Bundle\CronBundle\Annotation\CronJob;
    // ....
    
    /**
     * @CronJob("*\/5 * * * *")
     */
    class DemoCommand extends Command
    {
        // ...
    }
   ```

  After:
   ```php
    <?php
    
    // ....
    use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
    // ....
    
    #[AsCronJob('* * * * *')]
    class DemoCommand extends Command
    {
        // ...
    }
   ```
  
Commands
-----

* Removed `shapecode:cron:edit` command, use `shapecode:cron:enable` and `shapecode:cron:disable` instead.

Events
-----

* Removed `LoadJobsEvent::NAME` as event name. Use class name instead

Doctrine
-----

* Changed from annotations to attributes
* Register repositories as services