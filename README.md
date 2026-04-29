# Video Conversion Backend

Laravel + Lighthouse GraphQL backend for asynchronous video conversion.

The service accepts video uploads, creates conversion jobs, converts videos to browser-playable MP4 up to 720p via FFmpeg, exposes job status through GraphQL, and returns a download URL when conversion is completed.

## Stack

- PHP 8.4
- Laravel 12
- Lighthouse GraphQL
- MySQL 8.4
- Redis Queue
- FFmpeg
- Docker Compose
- PHPUnit / PHPStan / Laravel Pint

## Architecture

The project uses a lightweight Clean Architecture approach.

```txt
app/Modules
  VideoConversion
    Actions      Use cases / orchestration
    Tasks        Small focused operations
    Jobs         Async wrappers
    Events       Internal application events
    Listeners    Extension points
    GraphQL      API boundary
    Models       Persistence

  MediaConverter
    Contracts    Converter ports
    DTO          Stable contracts
    Infrastructure/Ffmpeg
                 Local FFmpeg adapter
```

GraphQL resolvers and jobs are intentionally thin.
Actions orchestrate use cases, tasks perform isolated operations, and FFmpeg is hidden behind `MediaConverterInterface`.

The converter is isolated so the local FFmpeg adapter can later be replaced with an external media-conversion service without changing GraphQL or the main application flow.

## Event-driven flow

The conversion lifecycle emits internal Laravel events:

- `VideoConversionCreated`
- `VideoConversionStarted`
- `VideoConversionCompleted`
- `VideoConversionFailed`

Post-processing is attached through listeners and separate jobs:

- thumbnail generation placeholder
- source cleanup placeholder
- integration event publishing placeholder
- failure logging

Two queues are used:

```txt
video-conversions       heavy FFmpeg jobs
video-post-processing   lightweight follow-up jobs
```

This allows conversion and post-processing workers to be scaled independently.

## Reliability

- Conversion runs asynchronously through Laravel Queue.
- Redis is used as the queue backend.
- Conversion jobs have retries, backoff and timeout.
- Final failures are persisted through the job `failed()` hook.
- Status transitions are stored in MySQL.
- Events are dispatched after database commit.

## Run locally

```bash
cp .env.example .env
docker compose up --build -d
```

App:

```txt
http://localhost:8080
```

GraphQL:

```txt
http://localhost:8080/graphql
```

The entrypoint prepares the app automatically:

- creates `.env` if missing
- generates `APP_KEY`
- creates the storage symlink
- runs migrations

Manual commands if needed:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan storage:link
docker compose exec app php artisan migrate
```

## GraphQL upload

```bash
curl http://localhost:8080/graphql \
  -F operations='{ "query": "mutation ($file: Upload!) { uploadVideo(file: $file) { uuid status progress originalFilename downloadUrl } }", "variables": { "file": null } }' \
  -F map='{ "0": ["variables.file"] }' \
  -F 0=@tmp/test-video.mov;type=video/quicktime
```

Example response:

```json
{
  "data": {
    "uploadVideo": {
      "uuid": "9b4f7c4a-86ef-4c11-a6e3-6f4d66b68c9f",
      "status": "queued",
      "progress": 0,
      "originalFilename": "test-video.mov",
      "downloadUrl": null
    }
  }
}
```

## Query status

```bash
curl -X POST http://localhost:8080/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query ($uuid: String!) { videoConversionJob(uuid: $uuid) { uuid status progress originalFilename downloadUrl failureReason createdAt startedAt completedAt failedAt } }",
    "variables": {
      "uuid": "PUT_UUID_HERE"
    }
  }'
```

Statuses:

```txt
queued
processing
completed
failed
```

When status is `completed`, `downloadUrl` contains the converted MP4 URL.

## Workers

Docker Compose starts workers automatically:

```txt
queue-video-conversions
queue-video-post-processing
```

Logs:

```bash
docker compose logs -f queue-video-conversions queue-video-post-processing
```

## Tests

```bash
docker compose exec app php artisan test
```

## Progress

Progress is stored at API and persistence level.

Current values:

```txt
queued       0%
processing  1%
completed   100%
failed       failure reason is stored
```
