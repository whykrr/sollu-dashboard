<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthCheckController extends Controller
{
    /**
     * Perform system health check across critical dependencies.
     */
    public function index(): JsonResponse
    {
        $services = [];
        $isHealthy = true;

        // 1. Database connection check
        try {
            $dbStart = microtime(true);
            DB::connection()->getPdo();
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);

            $services['database'] = [
                'status' => 'healthy',
                'latency_ms' => (float) $dbLatency,
            ];
        } catch (Throwable $e) {
            $isHealthy = false;
            $services['database'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }

        // 2. Cache / Redis probe check
        try {
            $cacheKey = 'healthcheck_probe_'.time();
            Cache::put($cacheKey, true, 10);
            $cacheCheck = Cache::get($cacheKey) === true;
            Cache::forget($cacheKey);

            if ($cacheCheck) {
                $services['cache'] = [
                    'status' => 'healthy',
                    'driver' => (string) config('cache.default'),
                ];
            } else {
                $isHealthy = false;
                $services['cache'] = [
                    'status' => 'unhealthy',
                    'error' => 'Cache write or read verification failed',
                ];
            }
        } catch (Throwable $e) {
            $isHealthy = false;
            $services['cache'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }

        // 3. Storage directory writable check
        $storagePath = storage_path('framework/cache');
        $isStorageWritable = is_writable($storagePath);
        if ($isStorageWritable) {
            $services['storage'] = [
                'status' => 'healthy',
            ];
        } else {
            $isHealthy = false;
            $services['storage'] = [
                'status' => 'unhealthy',
                'error' => 'Storage directory is not writable',
            ];
        }

        $statusCode = $isHealthy ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE;

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'environment' => (string) config('app.env'),
            'services' => $services,
        ], $statusCode);
    }
}
