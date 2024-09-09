<?php

namespace App\Kernel\Responses;

class BeatmapSet extends ApiEntity
{
    public string $artist;
    public string $artist_unicode;
    public Covers $covers;
    public string $creator;
    public int $id;
    public string $title;
    public string $title_unicode;
}
