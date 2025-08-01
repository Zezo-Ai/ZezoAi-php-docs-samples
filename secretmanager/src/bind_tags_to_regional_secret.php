<?php
/*
 * Copyright 2025 Google LLC.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * For instructions on how to run the full sample:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/main/secretmanager/README.md
 */

declare(strict_types=1);

namespace Google\Cloud\Samples\SecretManager;

// [START secretmanager_bind_tags_to_regional_secret]
// Import the Secret Manager client library.
use Google\Cloud\SecretManager\V1\CreateSecretRequest;
use Google\Cloud\SecretManager\V1\Secret;
use Google\Cloud\SecretManager\V1\Client\SecretManagerServiceClient;
use Google\Cloud\ResourceManager\V3\Client\TagBindingsClient;
use Google\Cloud\ResourceManager\V3\CreateTagBindingRequest;
use Google\Cloud\ResourceManager\V3\TagBinding;

/**
 * @param string $projectId  Your Google Cloud Project ID (e.g. 'my-project')
 * @param string $locationId Your Google Cloud Location ID (e.g. 'us-central1')
 * @param string $secretId   Your secret ID (e.g. 'my-secret')
 * @param string $tagValue   Your tag value (e.g. 'tagValues/281476592621530')
 */
function bind_tags_to_regional_secret(string $projectId, string $locationId, string $secretId, string $tagValue): void
{
    // Specify regional endpoint.
    $options = ['apiEndpoint' => "secretmanager.$locationId.rep.googleapis.com"];

    // Create the Secret Manager client.
    $client = new SecretManagerServiceClient($options);

    // Build the resource name of the parent project.
    $parent = $client->locationName($projectId, $locationId);

    $secret = new Secret();

    // Build the request.
    $request = CreateSecretRequest::build($parent, $secretId, $secret);

    // Create the secret.
    $newSecret = $client->createSecret($request);

    // Print the new secret name.
    printf('Created secret %s' . PHP_EOL, $newSecret->getName());

    // Specify regional endpoint.
    $tagBindOptions = ['apiEndpoint' => "$locationId-cloudresourcemanager.googleapis.com"];
    $tagBindingsClient = new TagBindingsClient($tagBindOptions);
    $tagBinding = (new TagBinding())
        ->setParent('//secretmanager.googleapis.com/' . $newSecret->getName())
        ->setTagValue($tagValue);

    // Build the request.
    $request = (new CreateTagBindingRequest())
        ->setTagBinding($tagBinding);

    // Create the tag binding.
    $operationResponse = $tagBindingsClient->createTagBinding($request);
    $operationResponse->pollUntilComplete();

    // Check if the operation succeeded.
    if ($operationResponse->operationSucceeded()) {
        printf('Tag binding created for secret %s with tag value %s' . PHP_EOL, $newSecret->getName(), $tagValue);
    } else {
        $error = $operationResponse->getError();
        printf('Error in creating tag binding: %s' . PHP_EOL, $error->getMessage());
    }
}
// [END secretmanager_bind_tags_to_regional_secret]

// The following 2 lines are only needed to execute the samples on the CLI
require_once __DIR__ . '/../../testing/sample_helpers.php';
\Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
