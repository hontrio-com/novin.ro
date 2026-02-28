<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $pages = [
            ['url' => url('/'),                              'priority' => '1.0',  'changefreq' => 'weekly',  'lastmod' => now()->toDateString()],
            ['url' => url('/termeni-si-conditii'),           'priority' => '0.3',  'changefreq' => 'yearly',  'lastmod' => now()->toDateString()],
            ['url' => url('/politica-de-confidentialitate'), 'priority' => '0.3',  'changefreq' => 'yearly',  'lastmod' => now()->toDateString()],
            ['url' => url('/politica-cookies'),              'priority' => '0.3',  'changefreq' => 'yearly',  'lastmod' => now()->toDateString()],
        ];

        $xml = view('sitemap', compact('pages'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}