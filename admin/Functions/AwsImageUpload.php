<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * Upload file to AWS S3
 *
 * @param string $filePath   Local file path
 * @param string $fileName   Name to save in S3
 * @param string $folder     (Optional) Folder inside bucket
 * @return string|false      Returns S3 URL on success, false on failure
 */
function uploadToS3($filePath, $fileName, $folder = '')
{
    $bucketName = 'vastranand-admin';
    $region = 'ap-south-1';
    $accessKey = 'AKIARKPKPSXCGHETMSXH';
    $secretKey = 'lpLxAFp18PDvmysyh74gMPNPgG';
     $keyPath = $folder.$fileName;
    $s3 = new S3Client([
        'region' => 'ap-south-1',
        'version' => 'latest',
        'credentials' => [
            'key' => 'AKIARKPKPSXCGHETMSXH',
            'secret' => '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',
        ],
    ]);

    $result = $s3->putObject([
        'Bucket' => 'vastranand-admin',
        'Key' => $keyPath,
        'SourceFile' => $filePath,
        'ACL' => 'private',
    ]);
    $image_url = $result['ObjectURL'];
    return $image_url;
}

function uploadVideoToS3($filePath, $fileName, $folder = '',$filetempName)
{
    $bucketName = 'vastranand-admin';
    $region = 'ap-south-1';
    $accessKey = 'AKIARKPKPSXCGHETMSXH';
    $secretKey = 'lpLxAFp18PDvmysyh74gMPNPgG';
     $keyPath = $folder.$fileName;
    $s3 = new S3Client([
        'region' => 'ap-south-1',
        'version' => 'latest',
        'credentials' => [
            'key' => 'AKIARKPKPSXCGHETMSXH',
            'secret' => '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',
        ],
    ]);

    $result = $s3->putObject([
        'Bucket' => 'vastranand-admin',
        'Key' => $keyPath,
        'SourceFile' => $filePath,
        'ContentType' => mime_content_type($filetempName),
        'ACL' => 'private',
    ]);
    $image_url = $result['ObjectURL'];
    return $image_url;
}
