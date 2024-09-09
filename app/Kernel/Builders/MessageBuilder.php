<?php

namespace App\Kernel\Builders;

class MessageBuilder
{
    private string $delimiter;
    private array $data;

    public function __construct(string $delimiter = ' \\| ')
    {
        $this->delimiter = $delimiter;
        $this->data = [];
    }

    public function addText(string $data): self
    {
        if (!empty($data)) {
            $this->data[] = $data;
        }

        return $this;
    }

    public function addLink(string $text, string $url): self
    {
        if (!empty($text) && !empty($url)) {
            $this->addText("[$text]($url)");
        }

        return $this;
    }

    public function getText(): string
    {
        return implode($this->delimiter, $this->data);
    }
}
