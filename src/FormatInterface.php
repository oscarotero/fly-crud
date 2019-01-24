<?php
declare(strict_types = 1);

namespace FlyCrud;

interface FormatInterface
{
    /**
     * Returns the file extension used by this format.
     */
    public function getExtension(): string;

    /**
     * Transform the data to a string.
     */
    public function stringify(array $data): string;

    /**
     * Transform the string to an array.
     */
    public function parse(string $source): array;
}
