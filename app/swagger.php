<?php
# A Swagger 2.0 (a.k.a. OpenAPI).
#
# Some style notes:
# - This file is used by ReDoc, which allows GitHub Flavored Markdown in
#   descriptions.
# - There is no maximum line length, for ease of editing and pretty diffs.
# - operationIds are in the format "NounVerb", with a singular noun.
#
# Based on https://docs.docker.com/engine/api/v1.25/swagger.yaml

/**
 * @SWG\Swagger(
 *     swagger="2.0",
 *     schemes={"https"},
 *     host="EnnerVoice.com",
 *     basePath="/api",
 *     produces={"application/json"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="EnnerVoice API",
 *         description="
Interact with FmDate server with This API.

Specify response content type to always recieve json responses by sending header:

    Accept: application/json

Specify Current Language:

    Accept-Language: ar

If you have already got an api token from the [`/login` endpoint](#tag/Public),  you can just pass this in `Authorization` header:

    Authorization: Bearer my_secret_api_token

The API uses standard HTTP status codes to indicate the success or failure of the API call. The body of the response will be JSON in the following format:

    {
        ""error"": {
            ""status"": 4xx|5xx,
            ""name"": ""ErrorName"",
            ""description"": ""Error description."",
            ""details"": []
        }
    }
",
 *     ),
 *     @SWG\SecurityScheme(
 *         securityDefinition="Tokon API",
 *         type="apiKey",
 *         description="Required to access this resourse",
 *         name="Authorization",
 *         in="header"
 *     ),
 *     security={"ApiKeyAuth"},
 *     @SWG\Tag(
 *         name="Public",
 *         description="Those Endpoints doesn't need authentication."
 *     ),
 *     @SWG\Tag(
 *         name="Me",
 *         description="Me.",
 *     ),
 * )
 */
