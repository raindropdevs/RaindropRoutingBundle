<?php

namespace Raindrop\RoutingBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DropSchemaDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
//use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
//use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class BaseTestCase extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected static $em;

    static protected function createKernel(array $options = array())
    {
        return new AppKernel(
            isset($options['config']) ? $options['config'] : 'default.yml'
        );
    }

    /**
     * careful: the kernel is shut down after the first test, if you need the
     * kernel, recreate it.
     *
     * @param array $options passed to self::createKernel
     * @param string $routebase base name for routes under /test to use
     */
    public static function setupBeforeClass(array $options = array())
    {
        self::$kernel = self::createKernel($options);
        self::$kernel->init();
        self::$kernel->boot();

        self::$em = self::$kernel->getContainer()->get('doctrine')->getManager();


        $application = new Application(self::$kernel);
 

        // add the database:drop command to the application and run it
//        $command = new DropDatabaseDoctrineCommand();
//        $application->add($command);
//        $input = new ArrayInput(array(
//            'command' => 'doctrine:database:drop',
//            '--force' => true,
//        ));
//        $command->run($input, new ConsoleOutput());
        
//        var_dump(self::$kernel->getContainer()->get('doctrine')->getConnection()->connect());
//        
//        var_dump(get_class(self::$kernel->getContainer()->get('doctrine')->getConnection()));
//        var_dump(self::$kernel->getContainer()->get('doctrine')->getConnection()->getDatabase());
//        die();
        
        
        
        // add the database:create command to the application and run it
//        $command = new CreateDatabaseDoctrineCommand();
//        $application->add($command);
//        $input = new ArrayInput(array(
//            'command' => 'doctrine:database:create',
//        ));
//        $command->run($input, new ConsoleOutput());

        $command = new DropSchemaDoctrineCommand();
        $application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:drop',
            '--force' => true
        ));
        $command->run($input, new ConsoleOutput());

        // let Doctrine create the database schema (i.e. the tables)
        $command = new CreateSchemaDoctrineCommand();
        $application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:update',
        ));
        $command->run($input, new ConsoleOutput());
    }
}