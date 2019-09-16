<?php

namespace Pantherify\GraphQLGenerator\Commands;

use Generator;
use Illuminate\Console\Command;
use Pantherify\GraphQLGenerator\src\Generators\GraphQLGenerator;
use Pantherify\GraphQLGenerator\src\Generators\GraphQLSchemaGenerator;

/**
 * Class GraphQLGeneratorCommand
 * @package Pantherify\GraphQLGenerator\Commands
 */
class GraphQLGeneratorCommand extends Command
{


    private $queryOptions = [
        'models' => false,
        'pagination' => true,
        'queries' => true,
        'pretty' => false,
        'mutations' => false,
        'auth' => true,
        'delete' => false
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        '
            graphql-gen:generate-models 
                {--Q|queries : Generate Query Files}
                {--D|delete : Clear directories before generating files}
                {--P|pretty : Run Prettier at the End - Required Prettier to be installed globally}
                {--no-models : Skip Model Generation}
                {--no-pagination : Does not include pagination in Listing Queries}
                {--M|mutations : Generate Mutation Files (Create, Update, Delete)}
                {--no-auth-mutation : Skip Authentication Implementation in Mutations}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates GraphQL Model Schemas based on Eloquent Models';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Pantherify\GraphQLGenerator\src\Exceptions\GeneratorException
     */
    public function handle()
    {
        $this->processArguments();

        $hasDoctrine = interface_exists('Doctrine\DBAL\Driver');
        if (!$hasDoctrine) {
            $this->error('Doctrine not installed!');
            $this->info("Install it with : \n composer require doctrine/dbal");
            return;
        }
        // Get Schema
        $schema = iterator_to_array(GraphQLGenerator::parseModels());

        // Generate Model Files
        if ($this->queryOptions['models']) {
            $this->warn('Generating Models');

            $this->printOutput(
                GraphQLSchemaGenerator::createModelFiles($schema, $this->queryOptions)
            );

        }

        if ($this->queryOptions['queries']) {
            $this->warn('Generating Queries');

            $this->printOutput(
                GraphQLSchemaGenerator::createQueryFiles($schema, $this->queryOptions)
            );
        }

        if ($this->queryOptions['mutations']) {
            $this->warn('Generating Mutations');

            $this->printOutput(
                GraphQLSchemaGenerator::createMutationFiles($schema, $this->queryOptions)
            );
        }


        if ($this->queryOptions['pretty']) {

            $this->warn('Running Prettier');
            system('prettier --write graphql/generated/**/*.graphql');
            system('prettier --write graphql/generated/**/**/*.graphql');
        }

    }


    private function processArguments()
    {
        $this->queryOptions['queries'] = $this->option('queries');
        $this->queryOptions['delete'] = $this->option('delete');
        $this->queryOptions['pretty'] = $this->option('pretty');
        $this->queryOptions['models'] = !$this->option('no-models');
        $this->queryOptions['pagination'] = !$this->option('no-pagination');
        $this->queryOptions['mutations'] = $this->option('mutations');
        $this->queryOptions['auth'] = !$this->option('no-auth-mutation');
    }

    private function printOutput(Generator $generator)
    {
        foreach ($generator as $message) $this->info($message);
        $this->line("");
    }

}
