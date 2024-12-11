<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProcessPaymentsSlipFileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function testShouldUploadFileToProcessInBackground(): void
    {
        $response = $this->post('/api/payment-slip/process-file', [
            'file' => UploadedFile::fake()->createWithContent(
                'input.csv',
                file_get_contents(__DIR__ . "/../Unit/Fixtures/input.csv")
            )
        ]);

        $response->assertStatus(200);
        $json = $response->decodeResponseJson();

        # Aqui ja foi testado o processamento do listener :)

        $this->assertEquals(
            "Success, your file has been imported and is processing",
            $json->json()['message']
        );
    }
}
