<?php

declare(strict_types=1);

namespace RunApi\GptImage2;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\GptImage2\Resources\EditImage;
use RunApi\GptImage2\Resources\TextToImage;

/**
 * The GPT Image 2 image generation API client.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
final class GptImage2Client extends BaseClient
{
    /**
     * Provides text-to-image generation operations.
     */
    public readonly TextToImage $textToImage;
    /**
     * Provides image editing operations using source images as context.
     */
    public readonly EditImage $editImage;

    /**
     * Create a GPT Image 2 client with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->textToImage = TextToImage::fromHttp($this->http);
        $this->editImage = EditImage::fromHttp($this->http);
    }
}
