<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class DocsController extends Controller
{
    /**
     * Documents that must never be served through the portal (commercially
     * sensitive). These are blocked even on direct URL access.
     *
     * @var array<int, string>
     */
    private array $restricted = [
        'HMS_Project_Costing_Proposal.md',
    ];

    /**
     * Absolute path to the documentation folder (outside the web root).
     */
    private function docsPath(): string
    {
        return base_path('docs');
    }

    /**
     * Serve the documentation portal index.
     */
    public function index(): SymfonyResponse
    {
        return $this->serve('index.html');
    }

    /**
     * Serve a single documentation file by name.
     *
     * - .html files are returned as-is (they already carry the brand theme).
     * - .md files are rendered to HTML and wrapped in the brand theme so links
     *   from the index navigate to readable pages instead of raw markdown.
     * - assets (css) are served with the correct content type.
     */
    public function show(string $file): SymfonyResponse
    {
        return $this->serve($file);
    }

    /**
     * Serve an asset (e.g. the shared theme stylesheet) from docs/assets.
     */
    public function asset(string $file): SymfonyResponse
    {
        $path = $this->docsPath().'/assets/'.$file;

        abort_unless($this->isSafe($file) && File::exists($path), 404);

        return response(File::get($path), 200, [
            'Content-Type' => Str::endsWith($file, '.css') ? 'text/css' : File::mimeType($path),
        ]);
    }

    private function serve(string $file): SymfonyResponse
    {
        abort_unless($this->isSafe($file), 404);

        // Never serve restricted (commercially sensitive) documents.
        abort_if(in_array($file, $this->restricted, true), 404);

        $path = $this->docsPath().'/'.$file;

        abort_unless(File::exists($path), 404);

        // Render Markdown into the branded HTML shell.
        if (Str::endsWith($file, '.md')) {
            return response($this->renderMarkdown($path), 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }

        // HTML documents are already themed — rewrite relative asset/doc links
        // to go through the /docs routes so they resolve behind auth.
        if (Str::endsWith($file, '.html')) {
            return response($this->rewriteLinks(File::get($path)), 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }

        return response(File::get($path), 200, [
            'Content-Type' => File::mimeType($path) ?: 'application/octet-stream',
        ]);
    }

    /**
     * Convert a markdown file to a full themed HTML page.
     */
    private function renderMarkdown(string $path): string
    {
        $title = Str::of(basename($path, '.md'))->replace('_', ' ')->title();

        $html = Str::markdown(File::get($path), [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HMS — {$title}</title>
<link rel="stylesheet" href="/docs/assets/hms-theme.css">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="shortcut icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<meta name="theme-color" content="#006838">
</head>
<body>
<div class="masthead">
    <div class="logo-mark">
        <div class="cross"></div>
        <div class="brand-name">HMS · Collin Saunders Hospital</div>
    </div>
    <h1>{$title}</h1>
    <div class="accent-bar"></div>
</div>
<div class="doc markdown-body">
    <p><a class="doc-card" style="padding:10px 16px;margin-bottom:24px;" href="/docs">&larr; Back to Documentation Portal</a></p>
    {$html}
    <div class="doc-footer">
        Collin Saunders Hospital — Hospital Management System Documentation · v1.0 · Confidential
    </div>
</div>
</body>
</html>
HTML;
    }

    /**
     * Rewrite relative links in themed HTML docs so they resolve under /docs,
     * and inject the brand favicon tags into the <head>.
     */
    private function rewriteLinks(string $html): string
    {
        // assets/hms-theme.css → /docs/assets/hms-theme.css
        $html = str_replace('href="assets/', 'href="/docs/assets/', $html);

        // Cross-links between docs (href="01_..." / "HMS_...") → /docs/{file}
        $html = preg_replace('/href="((?:\d{2}_|HMS_)[^"]+)"/', 'href="/docs/$1"', $html);

        // Inject favicon links just before </head> so every doc page shows the brand icon.
        $favicon = <<<'FAVICON'
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="shortcut icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<meta name="theme-color" content="#006838">
FAVICON;

        $html = str_replace('</head>', $favicon."\n</head>", $html);

        return $html;
    }

    /**
     * Guard against path traversal: only allow simple filenames.
     */
    private function isSafe(string $file): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9._-]+$/', $file) && ! Str::contains($file, '..');
    }
}
