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
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/main/parametermanager/README.md
 */

declare(strict_types=1);

namespace Google\Cloud\Samples\ParameterManager;

// [START parametermanager_enable_regional_param_version]
// Import necessary classes for enable a parameter version.
use Google\Cloud\ParameterManager\V1\Client\ParameterManagerClient;
use Google\Cloud\ParameterManager\V1\ParameterVersion;
use Google\Cloud\ParameterManager\V1\UpdateParameterVersionRequest;
use Google\Protobuf\FieldMask;

/**
 * Enables a regional parameter version using the Parameter Manager SDK for GCP.
 *
 * @param string $projectId The Google Cloud Project ID (e.g. 'my-project')
 * @param string $locationId The Parameter Location (e.g. 'us-central1')
 * @param string $parameterId The Parameter ID (e.g. 'my-param')
 * @param string $versionId The Version ID (e.g. 'my-param-version')
 */
function enable_regional_param_version(string $projectId, string $locationId, string $parameterId, string $versionId): void
{
    // Specify regional endpoint.
    $options = ['apiEndpoint' => "parametermanager.$locationId.rep.googleapis.com"];

    // Create a client for the Parameter Manager service.
    $client = new ParameterManagerClient($options);

    // Build the resource name of the parameter version.
    $parameterVersionName = $client->parameterVersionName($projectId, $locationId, $parameterId, $versionId);

    // Update the parameter version.
    $parameterVersion = (new ParameterVersion())
        ->setName($parameterVersionName)
        ->setDisabled(false);

    $updateMask = (new FieldMask())
        ->setPaths(['disabled']);

    // Prepare the request to enable the parameter version.
    $request = (new UpdateParameterVersionRequest())
        ->setUpdateMask($updateMask)
        ->setParameterVersion($parameterVersion);

    // Enable the parameter version using the client.
    $client->updateParameterVersion($request);

    // Print the parameter version details.
    printf('Enabled regional parameter version %s for parameter %s' . PHP_EOL, $versionId, $parameterId);
}
// [END parametermanager_enable_regional_param_version]

// The following 2 lines are only needed to execute the samples on the CLI
require_once __DIR__ . '/../../testing/sample_helpers.php';
\Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
