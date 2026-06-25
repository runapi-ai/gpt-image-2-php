<?php

declare(strict_types=1);

namespace RunApi\GptImage2\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\GptImage2\Models\CompletedImageTaskResponse;
use RunApi\GptImage2\Models\ImageTaskResponse;
use RunApi\GptImage2\Types;

/**
 * Generates images from text prompts, with optional aspect ratio and resolution controls.
 */
readonly class TextToImage extends TypedConfiguredResource
{
    /**
     * Submits a text-to-image generation task and returns a task reference for polling.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   callback_url?: string,
     *   output_resolution?: string
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Retrieves the current state and results of a text-to-image task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): ImageTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var ImageTaskResponse $response */
        return $response;
    }

    /**
     * Submits a text-to-image task and polls until it completes or fails.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   callback_url?: string,
     *   output_resolution?: string
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedImageTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedImageTaskResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/gpt_image_2/text_to_image',
            'gpt-image-2/text-to-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
            Types::TEXT_TO_IMAGE_MODELS,
            'text-to-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
        );
    }
}
