<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateMatchReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-match-reminders {days=3 : Days before match to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create reminders for upcoming matches';

    /**
     * The notification service instance.
     */
    protected $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->argument('days');
        
        $this->info("Creating match reminders for games {$days} days from now...");
        
        $count = $this->notificationService->createMatchReminders($days);
        
        $this->info("Created {$count} match reminders.");
        Log::info("Created {$count} match reminders for games {$days} days from now.");
        
        return Command::SUCCESS;
    }
}
