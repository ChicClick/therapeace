<?php

require 'vendor/autoload.php';

use Aws\Exception\AwsException;
use Aws\S3\S3Client;

class AwsS3
{
    private $bucketName;
    private $region;
    private $accessKey;
    private $secretKey;

    public function __construct($type = "default")
    {
        $this->bucketName = $type === "image" ? getenv('BUCKET_NAME_PATTIENT_IMAGE') : getenv('BUCKET_NAME');
        $this->region = getenv('REGION');
        $this->accessKey = getenv('ACCESS_KEY');
        $this->secretKey = getenv('SECRET_ACCESS_KEY');
    }

    private function createS3Client()
    {
        return new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'key' => $this->accessKey,
                'secret' => $this->secretKey,
            ],
            'http' => [
                'verify' => false,
            ],
        ]);
    }

    public function uploadFile($filePath, $key)
    {
        try {
            $s3Client = $this->createS3Client();
            $result = $s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key' => $key,
                'SourceFile' => $filePath,
                'ACL' => 'private',
            ]);

            return $result->get('ObjectURL');
        } catch (AwsException $e) {
            echo "Error uploading file: " . $e->getMessage();
            return null;
        }
    }
}
