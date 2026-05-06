<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ $baseUrl }}/</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    @foreach($articles as $article)
    <url>
        <loc>{{ $baseUrl }}/article/{{ $article->id }}</loc>
        <lastmod>{{ $article->updated_at->format('Y-m-d') }}</lastmod>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
    </url>
    @endforeach
</urlset>
