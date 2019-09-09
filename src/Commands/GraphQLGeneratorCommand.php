<?php

namespace Pantherify\GraphQLGenerator\Commands;

use Illuminate\Console\Command;
use Pantherify\GraphQLGenerator\src\Generators\GraphQLGenerator;
use Pantherify\GraphQLGenerator\src\Generators\GraphQLSchemaGenerator;

class GraphQLGeneratorCommand extends Command
{


    private $queryOptions = [
        'models' => false,
        'queries' => true
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
                {--skip-models : Skip Model Generation}
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
        $schema = GraphQLGenerator::generateModels();

        // Generate Model Files
        if($this->queryOptions['models']) {
            $messages = GraphQLSchemaGenerator::createModelFiles(iterator_to_array($schema));
            foreach ($messages as $message) $this->info($message);
        }

        if ($this->queryOptions['queries']) {
            $messages = GraphQLSchemaGenerator::createQueryFiles(iterator_to_array($schema));
            foreach ($messages as $message) $this->info($message);
        }
    }


    private function processArguments() {
        $this->queryOptions['queries'] = $this->option('queries');
        $this->queryOptions['models'] = !$this->option('skip-models');

        $this->info(json_encode($this->queryOptions));
    }
}
