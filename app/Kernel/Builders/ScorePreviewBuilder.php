<?php

namespace App\Kernel\Builders;

use App\Models\File;
use App\Models\Score;
use GdImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScorePreviewBuilder
{
    private Score $score;
    private GdImage $background;
    private ?GdImage $avatar = null;
    private string $fontPath;
    private false|int $whiteColor;
    private false|int $blackColor;

    public function __construct(Score $score)
    {
        $this->score = $score;
        $this->prepareStaticImages();
        $this->whiteColor = imagecolorallocate($this->background, 255, 255, 255);
        $this->blackColor = imagecolorallocate($this->background, 0, 0, 0);
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
        $cover = $this->resize(imagecreatefromjpeg($this->score->beatmapset['covers']['cover']), 750, 209);
        $background = imagecreatefrompng(resource_path('/images/gd/bg.png'));

        $temp = imagecreatetruecolor(750, 250);
        imagealphablending($temp, false);
        imagesavealpha($temp, true);
        $transparent = imagecolorallocatealpha($temp, 255, 255, 255, 127);
        imagefilledrectangle($temp, 0, 0, 750, 250, $transparent);

        imagecopy($temp, $cover, 0, 80, 0, 0, 750, 170);
        imagealphablending($temp, true);

        imagecopy($temp, $background, 0, 0, 0, 0, 750, 250);

        $this->background = $temp;
        if (!empty($this->score->user->avatar_url)) {
            $info = pathinfo($this->score->user->avatar_url);
            $image = null;
            switch ($info['extension']) {
                case 'png':
                    $image = imagecreatefrompng($this->score->user->avatar_url);
                    break;
                case 'jpeg':
                case 'jpg':
                    $image = imagecreatefromjpeg($this->score->user->avatar_url);
                    break;
            }
            if ($image) {
                $this->avatar = $this->resize($image, 60, 60);
                imagedestroy($image);
            }
        }

        imagedestroy($cover);
        imagedestroy($background);
    }

    private function setAvatar(): void
    {
        if ($this->avatar) {
            imagecopy($this->background, $this->avatar, 20, 10, 0, 0, 60, 60);
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
        $this->setCenterText($mapName, $this->background, 375, 145, $this->whiteColor, 18);
    }

    private function addNickname(): void
    {
        if (empty($this->score->user->name)) {
            return;
        }

        $nickname = $this->score->user->name;
        [$nicknameWidth, $nicknameHeight] = $this->getTextSize($nickname, 16);
        $i = 0;
        while ($nicknameWidth > 80) {
            $nickname = substr($nickname, 0, -1 - $i) . '...';
            [$nicknameWidth, $nicknameHeight] = $this->getTextSize($nickname, 16);
            $i++;
        }
        imagettftext($this->background, 16, 0, 100, 34, $this->whiteColor, $this->fontPath, $nickname);
    }

    private function addRank(): void
    {
        $rank = $this->score->rank;
        $rankIconPath = resource_path("/images/gd/ranks/$rank.png");
        if (file_exists($rankIconPath)) {
            $rankImage = $this->resize(imagecreatefrompng($rankIconPath), 35, 17.5);
            imagecopy($this->background, $rankImage, 100, 47.5, 0, 0, 35, 17.5);
        }
    }

    private function addDiff(): void
    {
        $difficulty = round($this->score->beatmap['difficulty_rating'], 2);
        imagettftext($this->background, 9, 0, 368, 85, $this->whiteColor, $this->fontPath, $difficulty);
    }

    private function mapInfo(): void
    {
        $ar = "AR: {$this->score->beatmap['ar']}";
        $cs = "CS: {$this->score->beatmap['cs']}";
        $od = "OD: {$this->score->beatmap['drain']}";
        $bpm = "BPM: {$this->score->beatmap['bpm']}";

        $this->setCenterText($ar, $this->background, 94, 232, $this->blackColor, 18);
        $this->setCenterText($cs, $this->background, 283, 232, $this->blackColor, 18);
        $this->setCenterText($od, $this->background, 466.6, 232, $this->blackColor, 18);
        $this->setCenterText($bpm, $this->background, 650, 232, $this->blackColor, 18);
    }

    private function setCenterText(string $text, GdImage $image, float $x, float $y, int $color, float $size): void
    {
        [$textWidth, $textHeight] = $this->getTextSize($text, $size);
        imagettftext($image, $size, 0, $x - $textWidth / 2, $y + $textHeight / 2, $color, $this->fontPath, $text);
    }

    private function addScoreInfo(): void
    {
        $ppString = round($this->score->pp, 2) . 'pp';
        $this->setCenterText($ppString, $this->background, 270, 36, $this->whiteColor, 18);

        if ($this->score->score > 0) {
            $scoreString = number_format($this->score->score, 0, '', ' ');
            $this->setCenterText($scoreString, $this->background, 270, 15, $this->whiteColor, 10);
        }

        $accuracyString = round($this->score->accuracy * 100, 2) . '%';
        $this->setCenterText($accuracyString, $this->background, 275, 70, $this->whiteColor, 10);
    }

    private function addStatistics(): void
    {
        $combo = "Max combo: {$this->score->max_combo}";
        $this->setCenterText($combo, $this->background, 420, 18, $this->whiteColor, 10);

        $this->setCenterText($this->score->statistics['count_100'], $this->background, 378.5, 58, $this->whiteColor, 8);
        $this->setCenterText($this->score->statistics['count_50'], $this->background, 420.5, 58, $this->whiteColor, 8);
        $this->setCenterText($this->score->statistics['count_miss'], $this->background, 462.5, 58, $this->whiteColor, 8);
    }

    private function addMods(): void
    {
        if (!empty($this->score->mods)) {
            $mods = '+' . implode('', $this->score->mods);
            [$modsWidth, $modsHeight] = $this->getTextSize($mods, 18);
            imagettftext($this->background, 18, 0, 750 - 20 - $modsWidth, 80 / 2 + $modsHeight / 2, $this->whiteColor, $this->fontPath, $mods);
        }
    }

    public function getPreview(): ?File
    {
        if ($this->score->preview) {
            return $this->score->preview;
        }

        $this->setAvatar();
        $this->addText();
        $this->addNickname();
        $this->addRank();
        $this->addScoreInfo();
        $this->addDiff();
        $this->mapInfo();
        $this->addStatistics();
        $this->addMods();

        $fileName = Str::random() . '.png';
        $folderName = substr($fileName, 0, 3);
        $path = "/$folderName/$fileName";
        if (!Storage::disk('public')->exists($folderName)) {
            Storage::disk('public')->makeDirectory($folderName);
        }
        $filePath = Storage::disk('public')->path($path);
        if (imagepng($this->background, $filePath)) {
            $preview = File::create([
                'name' => $fileName,
                'path' => $path,
                'extension' => 'png'
            ]);
            if ($preview) {
                $this->score->preview()->associate($preview)->save();
                return $preview;
            }
        }

        return null;
    }
}
