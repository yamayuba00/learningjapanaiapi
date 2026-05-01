<?php

namespace App\Console\Commands;

use App\Services\Mobile\LeaderboardService;
use Illuminate\Console\Command;

class ResetMonthlyLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:reset-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and recalculate monthly leaderboard';

    protected $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        parent::__construct();
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly leaderboard reset...');
        
        try {
            $this->leaderboardService->calculateMonthlyLeaderboard();
            $this->info('Monthly leaderboard reset completed successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to reset monthly leaderboard: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}