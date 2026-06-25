<?php

declare(strict_types=1);

namespace RunApi\GptImage2;

/**
 * Constants for model slugs supported by the GPT Image 2 PHP SDK.
 */
final class Types
{
    /** @var list<string> */
    public const TEXT_TO_IMAGE_MODELS = ['gpt-image-2'];

    /** @var list<string> */
    public const EDIT_IMAGE_MODELS = ['gpt-image-2'];

    private function __construct()
    {
    }
}
