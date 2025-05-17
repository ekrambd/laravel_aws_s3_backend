<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Aws\S3\S3Client;
use App\Models\File;

class UploadFileToS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */ 
    protected $file_id, $key, $localPath;

    public function __construct(array $params)
    {   
        $this->file_id = $params['file_id'];
        $this->key = $params['key'];
        $this->localPath = $params['local_path'];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {   

        ini_set('max_execution_time', 3600);

        $file = getFile($this->file_id);

        $acl = $file->status === 'Public' ? 'public-read' : 'private';

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('services.ses.region'),
            'credentials' => [
                'key' => config('services.ses.key'),
                'secret' => config('services.ses.secret'),
            ],
        ]);

        $finalKey = $file->folder_id == NULL?$this->key:$file->folder->folder_slug.$this->key;

        try {

            $create = $s3Client->createMultipartUpload([
                'Bucket' => $file->bucket->bucket_slug,
                'Key' => $finalKey,
                'ContentType' => mime_content_type($this->localPath),
                'StorageClass' => $file->storage_class,
                'ACL' => $acl,
                'Tagging' => http_build_query([
                    'Public' => $file->status === 'Public' ? 'true' : 'false',
                ]),
            ]);

            $uploadId = $create['UploadId'];
            $partSize = 5 * 1024 * 1024;
            $parts = [];
            $partNumber = 1;

            $fileStream = fopen($this->localPath, 'rb');

            while (!feof($fileStream)) {
                $data = fread($fileStream, $partSize);

                $uploadPart = $s3Client->uploadPart([
                    'Bucket' => $file->bucket->bucket_slug,
                    'Key' => $finalKey, 
                    'UploadId' => $uploadId,
                    'PartNumber' => $partNumber,
                    'Body' => $data,
                ]);

                $parts[] = [
                    'PartNumber' => $partNumber,
                    'ETag' => $uploadPart['ETag'],
                ];

                $partNumber++;
            }

            fclose($fileStream);

            $result = $s3Client->completeMultipartUpload([
                'Bucket' => $file->bucket->bucket_slug,
                'Key' => $finalKey,
                'UploadId' => $uploadId,
                'MultipartUpload' => ['Parts' => $parts],
            ]);

            $file->bucket_url = $result['Location'];
            $file->file_key = $finalKey;
            $file->update();

            if (file_exists($this->localPath)) {
                unlink($this->localPath);
            }

            \Log::info("Upload to S3 complete for: {$finalKey}");

        } catch (\AwsException $e) {
            if (isset($uploadId)) {
                $s3Client->abortMultipartUpload([
                    'Key' => $this->key,
                    'UploadId' => $uploadId,
                ]);
            }

            \Log::error("S3 upload failed: " . $e->getMessage());
        }
    }
}
