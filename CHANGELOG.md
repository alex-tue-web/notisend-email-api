# Changelog

Все значимые изменения этого пакета документируются в этом файле.

Формат основан на [Keep a Changelog](https://keepachangelog.com/ru/1.1.0/),
версии следуют [Semantic Versioning](https://semver.org/lang/ru/).

## [1.1.0] - 2026-11-17
### Added
- Campaigns resource (`Campaigns` class, `Client::campaigns()`): create, all, get, deliver, schedule, cancel.
- `statistic` boolean query parameter is serialized as `true`/`false` in `Campaigns::all()`.

## [1.0.0] - 2026-11-16
### Added
- Initial release: Client, ApiException, Balance, Messages, Templates, Lists, Parameters, Recipients.
- Bearer-token authentication.
- Multipart/form-data support for attachments.
- Structured API errors via ApiException::getErrors().
