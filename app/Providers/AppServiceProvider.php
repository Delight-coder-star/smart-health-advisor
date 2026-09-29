<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Services\SentimentAnalysisService;
use App\Services\RiskAssessmentService;
use App\Services\RecommendationService;
use App\Services\VideoAnalysisService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SentimentAnalysisService::class);
        $this->app->singleton(RiskAssessmentService::class);
        $this->app->singleton(RecommendationService::class);
        $this->app->singleton(VideoAnalysisService::class);
    }

    public function boot(): void
    {
        Blade::directive('riskBadge', function ($expression) {
            return "<?php echo '<span class=\"risk-badge risk-' . $expression . '\">' . ucfirst($expression) . '</span>'; ?>";
        });
    }
}