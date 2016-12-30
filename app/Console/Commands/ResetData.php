<?php namespace App\Console\Commands;


use Utils, Artisan;
use Illuminate\Console\Command;

/**
 * Class ResetData
 */
class ResetData extends Command
{

    /**
     * @var string
     */
    protected $name = 'ninja:reset-data';
    
    /**
     * @var string
     */
    protected $description = 'Reset data';

    public function fire()
    {

        if (!Utils::isNinjaDev()) {
	        $this->warn(date('Y-m-d') . ' Cannot run in production');
            return;
        }
        
        $this->info(date('Y-m-d') . ' Running ResetData...');

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }
}