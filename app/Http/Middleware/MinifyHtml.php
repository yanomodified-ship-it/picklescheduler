<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Keep source readable while debugging, and never touch admin pages or JSON
        if (config('app.debug') || $request->is('admin', 'admin/*') || $request->expectsJson()) {
            return $response;
        }

        $type = (string) $response->headers->get('Content-Type', '');
        if (! str_contains($type, 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || $html === '') {
            return $response;
        }

        $min = $this->minify($html);
        if ($min !== null) {
            $response->setContent($min);
        }

        return $response;
    }

    private function minify(string $html): ?string
    {
        $parked = [];

        // 1) Park the blocks where whitespace matters, compacting scripts and styles safely
        $work = preg_replace_callback(
            '#<(script|style|pre|textarea)\b[^>]*>.*?</\1>#is',
            function ($m) use (&$parked) {
                $block = $m[0];
                $tag = strtolower($m[1]);

                if ($tag === 'script') {
                    $block = $this->compactScript($block);
                } elseif ($tag === 'style') {
                    $block = $this->compactStyle($block);
                }

                $key = '%%HCKEEP' . count($parked) . '%%';
                $parked[$key] = $block;

                return $key;
            },
            $html
        );
        if ($work === null) {
            return null;
        }

        // 2) Remove HTML comments (keeps conditional comments and the jondev credit)
        $work = preg_replace('/<!--(?!\[|\s*Website by jondev)(?:(?!-->).)*-->/s', '', $work);
        if ($work === null) {
            return null;
        }

        // 3) Collapse all whitespace to a single space
        $work = preg_replace('/\s+/', ' ', $work);
        if ($work === null) {
            return null;
        }

        // 4) Put the parked blocks back
        return strtr(trim($work), $parked);
    }

    private function compactScript(string $block): string
    {
        $out = [];

        foreach (preg_split('/\R/', $block) as $line) {
            $line = trim($line);

            // drop blank lines and whole-line // comments; newlines are kept so the code stays valid
            if ($line === '' || str_starts_with($line, '//')) {
                continue;
            }

            $out[] = $line;
        }

        return implode("\n", $out);
    }

    private function compactStyle(string $block): string
    {
        $block = preg_replace('#/\*.*?\*/#s', '', $block) ?? $block;
        $block = preg_replace('/\s+/', ' ', $block) ?? $block;

        return preg_replace('/\s*([{};])\s*/', '$1', $block) ?? $block;
    }
}