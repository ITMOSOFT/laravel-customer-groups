# Changelog

All notable changes to `itmosoft/laravel-customer-groups` are documented here.

## [1.0.0] - 2026-11-16

### Added
- Customer group model with generated slugs, descriptions, active status, and metadata.
- Polymorphic customer-to-group memberships.
- `HasCustomerGroups` trait for customer Eloquent models.
- Manager APIs for creating, finding, adding, removing, syncing, and deleting groups.
- Active group listing and membership checks.
- Configurable database table prefix and customer model.
- PHPUnit test suite and GitHub Actions matrix for PHP 8.3 and 8.4.
