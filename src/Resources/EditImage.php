<?php

declare(strict_types=1);

namespace RunApi\GptImage2\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\GptImage2\Models\CompletedImageTaskResponse;
use RunApi\GptImage2\Models\ImageTaskResponse;

/**
 * Modifies images by applying prompt-described changes to 1-16 source images.
 */
readonly class EditImage extends TypedConfiguredResource
{
    /**
     * Submits an image editing task and returns a task reference for polling.
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
     * Retrieves the current state and results of an image editing task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): ImageTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var ImageTaskResponse $response */
        return $response;
    }

    /**
     * Submits an image editing task and polls until it completes or fails.
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
            '/api/v1/gpt_image_2/edit_image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
            'edit-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
        );
    }
}
