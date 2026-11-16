# Release instructions

## Repository

- Name: `laravel-customer-groups`
- Organization: `ITMOSOFT`
- Description: Customer segmentation and group membership management for Laravel applications.
- Composer package: `itmosoft/laravel-customer-groups`

## Validate locally

```bash
composer validate --strict
composer install
composer test
```

Do not publish until the complete test suite passes locally and all four GitHub Actions matrix jobs are green.

## Initialize and push

```bash
git init
git branch -M main
git add .
git commit -m "feat: add Laravel customer groups"
git remote add origin https://github.com/ITMOSOFT/laravel-customer-groups.git
git push -u origin main
```

## Tag

Only after CI passes:

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## GitHub Release

Title: `v1.0.0 - Initial Release`

Description:

```markdown
## Initial Release – Laravel Customer Groups

- Customer group management with names, slugs, descriptions, active status, and metadata.
- Polymorphic customer memberships.
- Customer model trait and Eloquent relationships.
- Add, remove, sync, and query customer group memberships.
- Configurable customer model and database table prefix.

### Compatibility
- PHP 8.3+
- Laravel 12 and 13

MIT License.
```

## Packagist

Submit `https://github.com/ITMOSOFT/laravel-customer-groups` at https://packagist.org/packages/submit after the GitHub repository and release are available.
