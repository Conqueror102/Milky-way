<?php

namespace App\Http\Middleware;

use App\Support\SiteContent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Swap in the business details an admin saved before any page renders.
 */
class ApplySiteContent
{
    public function __construct(protected SiteContent $content) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->content->applyToConfig();

        return $next($request);
    }
}
