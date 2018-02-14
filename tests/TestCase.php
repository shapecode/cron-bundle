<?php


namespace Shapecode\Bundle\CronBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{

    protected function tearDown()
    {
        \Mockery::close();
    }

}