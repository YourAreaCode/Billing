<?php namespace App\Console\Commands;


use Utils;
use Illuminate\Console\Command;

/**
 * Class ResetData
 */
class InstallDB extends Command
{

    /**
     * @var string
     */
    protected $name = 'ninja:install-db';
    
    /**
     * @var string
     */
    protected $description = 'Reset data';

    public function fire()
    {
        $this->info(date('Y-m-d') . ' Running Install...');

        if (!Utils::isNinjaDev()) {
            return;
        }

        Artisan::call('migrate');
        Artisan::call('db:seed');
    }
}