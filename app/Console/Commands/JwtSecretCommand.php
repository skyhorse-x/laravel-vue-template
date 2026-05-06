<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class JwtSecretCommand extends Command
{
    protected $signature = 'jwt:secret {--force : 强制覆盖现有密钥}';

    protected $description = '生成 JWT 密钥并更新到 .env 文件';

    public function handle(): int
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env 文件不存在，请先复制 .env.example 到 .env');
            return Command::FAILURE;
        }

        $key = Str::random(64);

        if (!$this->option('force') && env('JWT_SECRET')) {
            if (!$this->confirm('JWT_SECRET 已存在，确定要覆盖吗？')) {
                return Command::FAILURE;
            }
        }

        $this->updateEnv($key);
        $this->updateConfig();

        $this->info('JWT_SECRET 已生成并配置成功！');
        $this->line('密钥: ' . $key);

        return Command::SUCCESS;
    }

    private function updateEnv(string $key): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if (preg_match('/^JWT_SECRET=/m', $envContent)) {
            $envContent = preg_replace('/^JWT_SECRET=.*$/m', "JWT_SECRET={$key}", $envContent);
        } else {
            $envContent .= "\nJWT_SECRET={$key}\n";
        }

        file_put_contents($envPath, $envContent);
    }

    private function updateConfig(): void
    {
        if (function_exists('artisan')) {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        }
    }
}
