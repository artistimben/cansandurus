<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kullanıcı aktivitelerini loglar
 * Önemli işlemleri (POST, PUT, DELETE) kaydeder
 */
class LogActivity
{
    /**
     * İzlenecek route'lar (pattern)
     */
    protected array $logRoutes = [
        'downtime.*',
        'admin.*',
        'machine.*',
        'error-code.*',
        'user.*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Activity logging aktif mi?
        if (!config('security.activity_log.enabled', true)) {
            return $response;
        }

        // Sadece önemli HTTP methodları için log
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }

        // Route adını kontrol et
        $routeName = $request->route()?->getName();
        if (!$routeName || !$this->shouldLog($routeName)) {
            return $response;
        }

        // Log kaydı oluştur
        try {
            ActivityLog::createLog(
                userId: auth()->id(),
                action: $this->getActionName($request),
                description: $this->getDescription($request, $routeName),
                modelType: $this->getModelType($routeName),
                modelId: $request->route('id') ?? $request->input('id')
            );
        } catch (\Exception $e) {
            // Log hatası uygulamayı etkilemesin
            \Log::error('Activity log error: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Bu route loglanmalı mı?
     */
    protected function shouldLog(string $routeName): bool
    {
        foreach ($this->logRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * İşlem adını belirle
     */
    protected function getActionName(Request $request): string
    {
        return match($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown',
        };
    }

    /**
     * İşlem açıklamasını oluştur
     */
    protected function getDescription(Request $request, string $routeName): string
    {
        $action = $this->getActionName($request);
        $parts = explode('.', $routeName);
        $module = $parts[0] ?? 'unknown';

        return ucfirst($action) . ' - ' . ucfirst($module);
    }

    /**
     * Model tipini belirle
     */
    protected function getModelType(string $routeName): ?string
    {
        if (str_contains($routeName, 'downtime')) return 'DowntimeRecord';
        if (str_contains($routeName, 'machine')) return 'Machine';
        if (str_contains($routeName, 'error-code')) return 'ErrorCode';
        if (str_contains($routeName, 'user')) return 'User';
        
        return null;
    }
}
