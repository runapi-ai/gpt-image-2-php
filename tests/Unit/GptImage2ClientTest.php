<?php

declare(strict_types=1);

namespace RunApi\GptImage2\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\GptImage2\GptImage2Client;
use RunApi\GptImage2\Models\CompletedImageTaskResponse;
use RunApi\GptImage2\Resources\EditImage;
use RunApi\GptImage2\Resources\TextToImage;

final class GptImage2ClientTest extends TestCase
{
    public function testExposesTypedResources(): void
    {
        $client = new GptImage2Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(TextToImage::class, $client->textToImage);
        self::assertInstanceOf(EditImage::class, $client->editImage);
    }

    public function testCreatePostsCompactedBodyToCorrectPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1","task_replayed":true}')]);
        $client = new GptImage2Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $task = $client->textToImage->create([
            'model' => 'gpt-image-2',
            'prompt' => 'A product render',
            'callback_url' => '',
            'seed' => null]);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('task_1', $task->id);
        self::assertTrue($task->taskReplayed);
        self::assertSame('/api/v1/gpt_image_2/text_to_image', $transport->requests[0]->getUri()->getPath());
        self::assertSame('gpt-image-2', $body['model']);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('seed', $body);
    }

    public function testRunReturnsTypedCompletedResponseAndPreservesUnknownFields(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","images":[{"url":"https://file.runapi.ai/result"}],"extra_field":"kept","usage":{"cost":0.05}}')]);
        $client = new GptImage2Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->textToImage->run([
            'model' => 'gpt-image-2',
            'prompt' => 'A product render']);

        self::assertInstanceOf(CompletedImageTaskResponse::class, $result);
        self::assertSame('https://file.runapi.ai/result', $result->images[0]->url);
        self::assertSame('kept', $result->toArray()['extra_field']);
        self::assertSame('/api/v1/gpt_image_2/text_to_image/task_1', $transport->requests[1]->getUri()->getPath());
    }

    public function testCompletedResponseRequiresResultFiles(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","usage":{"cost":0.05}}')]);
        $client = new GptImage2Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('images is required');

        $client->textToImage->run([
            'model' => 'gpt-image-2',
            'prompt' => 'A product render']);
    }


    public function testSecondaryResourceUsesItsOwnPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_2"}')]);
        $client = new GptImage2Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->editImage->create([
            'model' => 'gpt-image-2',
            'prompt' => 'A product render',
            'source_image_urls' => ['https://file.runapi.ai/source.png']]);

        self::assertSame('/api/v1/gpt_image_2/edit_image', $transport->requests[0]->getUri()->getPath());
    }
}
