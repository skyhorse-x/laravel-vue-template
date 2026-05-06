<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\CleanOldLogsJob;

class ScheduledTasks extends Command
{
    protected $signature = 'app:scheduled-tasks {action}';
    protected $description = 'Run scheduled tasks: clear-logs, clear-expired-ip, generate-sitemap';

    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'clear-logs':
                $days = (int) $this->option('days') ?: 30;
                Artisan::call('queue:work', ['--stop-when-empty' => true]);
                CleanOldLogsJob::dispatch($days);
                $this->info("Clean logs job dispatched for logs older than {$days} days");
                break;

            case 'clear-expired-ip':
                $this->clearExpiredIpBlacklist();
                break;

            case 'generate-sitemap':
                $this->generateSitemap();
                break;

            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }

        return 0;
    }

    private function clearExpiredIpBlacklist()
    {
        $count = \App\Models\IpBlacklist::where('expired_at', '<', now())->delete();
        $this->info("Cleared {$count} expired IP blacklist records");
    }

    private function generateSitemap()
    {
        $baseUrl = config('app.url', 'http://localhost');
        $articles = \App\Models\Article::where('status', 1)->get(['id', 'updated_at']);

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $sitemap .= "<url><loc>{$baseUrl}/</loc><priority>1.0</priority><changefreq>daily</changefreq></url>";

        foreach ($articles as $article) {
            $loc = "{$baseUrl}/article/{$article->id}";
            $lastmod = date('Y-m-d', strtotime($article->updated_at));
            $sitemap .= "<url><loc>{$loc}</loc><lastmod>{$lastmod}</lastmod><priority>0.8</priority><changefreq>weekly</changefreq></url>";
        }

        $sitemap .= '</urlset>';

        $path = public_path('sitemap.xml');
        file_put_contents($path, $sitemap);
        $this->info("Sitemap generated at {$path}");
    }
}
