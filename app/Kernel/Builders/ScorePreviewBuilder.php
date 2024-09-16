<?php

namespace App\Kernel\Builders;

use App\Models\Score;
use GdImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScorePreviewBuilder
{
    private Score $score;
    private GdImage $background;
    private ?GdImage $avatar;
    private GdImage $transparent;
    private ?GdImage $cover;
    private string $fontPath;
    private false|int $whiteColor;

    public function __construct(Score $score)
    {
        $this->score = $score;
        $this->prepareStaticImages();
        $this->whiteColor = imagecolorallocate($this->background, 255, 255, 255);
        $this->fontPath = resource_path('/fonts/exo-regular.ttf');
    }

    private function resize(GdImage $image, int $width, int $height): GdImage
    {
        $temp = imagecreatetruecolor($width, $height);
        imagealphablending($temp, false);
        imagesavealpha($temp, true);
        $transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
        imagefilledrectangle($temp, 0, 0, $width, $height, $transparent);
        imagecopyresampled($temp, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        return $temp;
    }

    private function prepareStaticImages(): void
    {
        $this->background = imagecreatefrompng(resource_path('/images/gd/bg.png'));
        $this->transparent = imagecreatefrompng(resource_path('/images/gd/transparent.png'));
        if (!empty($this->score->user->avatar_url)) {
            $this->avatar = $this->resize(imagecreatefromjpeg($this->score->user->avatar_url), 60, 60);
        }
        if (!empty($this->score->beatmapset['covers']['cover'])) {
            $this->cover = $this->resize(imagecreatefromjpeg($this->score->beatmapset['covers']['cover']), 1000, 260);
        }
    }

    private function setAvatar(): void
    {
        if ($this->avatar) {
            imagecopy($this->background, $this->avatar, 40, 10, 0, 0, 60, 60);
        }
    }

    private function setCover(): void
    {
        if ($this->cover) {
            imagecopy($this->background, $this->cover, 0, 80, 0, 0, 1000, 120);
            imagealphablending($this->background, true);
            imagesavealpha($this->background, true);
            imagealphablending($this->transparent, true);
            imagesavealpha($this->transparent, true);
            imagecopy($this->background, $this->transparent, 0, 80, 0, 0, imagesx($this->transparent), imagesy($this->transparent));
        }
    }

    /**
     * @param string $text
     * @param int $fontSize
     * @param string|null $fontPath
     * @return array [width, height]
     */
    private function getTextSize(string $text, int $fontSize, ?string $fontPath = null): array
    {
        $data = imagettfbbox($fontSize, 0, $fontPath ?? $this->fontPath, $text);

        return [abs($data[4] - $data[0]), abs($data[5] - $data[1])];
    }

    private function addText(): void
    {
        $mapName = "{$this->score->beatmapset['artist']} - {$this->score->beatmapset['title']} [{$this->score->beatmap['version']}]";
        [$mapNameWidth, $mapNameHeight] = $this->getTextSize($mapName, 22);
        imagettftext($this->background, 22, 0, round(1000 / 2) - round($mapNameWidth / 2), 80 + 60 + round($mapNameHeight / 2), $this->whiteColor, $this->fontPath, $mapName);
    }

    private function addNickname(): void
    {
        $nickname = $this->score->user->name;
        [$nicknameWidth, $nicknameHeight] = $this->getTextSize($nickname, 18);
        $i = 0;
        while ($nicknameWidth > 80) {
            $nickname = substr($nickname, 0, -1 - $i) . '...';
            [$nicknameWidth, $nicknameHeight] = $this->getTextSize($nickname, 18);
            $i++;
        }
        imagettftext($this->background, 18, 0, 120, 35, $this->whiteColor, $this->fontPath, $nickname);
    }

    private function addRank(): void
    {
        $rank = $this->score->rank;
        $rankIconPath = resource_path("/images/gd/ranks/$rank.png");
        if (file_exists($rankIconPath)) {
            $rankImage = $this->resize(imagecreatefrompng($rankIconPath), 35, 18);
            imagecopy($this->background, $rankImage, 125, 45, 0, 0, 35, 18);
        }
    }

    private function addScoreInfo(): void
    {
        $ppString = round($this->score->pp, 2) . 'pp';
        [$ppStringWidth, $ppStringHeight] = $this->getTextSize($ppString, 18);
        imagettftext($this->background, 18, 0, 346 - round($ppStringWidth / 2), 48, $this->whiteColor, $this->fontPath, $ppString);

        $scoreString = number_format($this->score->score, 0, '', ' ');
        [$scoreStringWidth, $scoreStringHeight] = $this->getTextSize($scoreString, 10);
        imagettftext($this->background, 10, 0, 346 - round($scoreStringWidth / 2), 20, $this->whiteColor, $this->fontPath, $scoreString);

        $accuracyString = round($this->score->accuracy * 100, 2) . '%';
        [$accuracyStringWidth, $accuracyStringHeight] = $this->getTextSize($accuracyString, 10);
        imagettftext($this->background, 10, 0, 346 - round($accuracyStringWidth / 2), 70, $this->whiteColor, $this->fontPath, $accuracyString);
    }

    public function getPreview(): ?string
    {
        $this->setAvatar();
        $this->setCover();
        $this->addText();
        $this->addNickname();
        $this->addRank();
        $this->addScoreInfo();

        $fileName = Str::random();
        $folderName = substr($fileName, 0, 3);
        $path = "/$folderName/$fileName.png";
        if (!Storage::disk('public')->exists($folderName)) {
            Storage::disk('public')->makeDirectory($folderName);
        }
        $filePath = Storage::disk('public')->path($path);
        if (imagepng($this->background, $filePath)) {
            return Storage::disk('public')->url($path);
        }

        return null;
    }
}
