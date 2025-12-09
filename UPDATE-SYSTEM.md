# KR Toolkit Update System

This plugin uses GitHub for automatic updates.

## How It Works

1. The plugin checks GitHub repository for new releases
2. When a new release is published, WordPress detects it
3. Users see an update notification in their dashboard
4. They can update with one click, just like WordPress.org plugins

## GitHub Repository

https://github.com/kerkeniaziz/kr-toolkit

## For Users

Updates appear automatically in:
- Dashboard → Updates
- Plugins → Installed Plugins

Simply click "Update Now" when available.

## For Developers

### Creating a New Release

1. Update version numbers in:
   - `kr-toolkit.php` (plugin header)
   - `kr-toolkit.php` (KR_TOOLKIT_VERSION constant)
   - `readme.txt` (Stable tag)

2. Commit and push changes:
```bash
git add .
git commit -m "Version 1.2.5"
git push origin main
```

3. Create GitHub Release:
   - Go to: https://github.com/kerkeniaziz/kr-toolkit/releases
   - Click "Draft a new release"
   - Tag: `v1.2.5` (must start with 'v')
   - Title: `Version 1.2.5`
   - Description: Copy changelog from readme.txt
   - Click "Publish release"

4. GitHub automatically creates a ZIP file
5. WordPress sites check for updates every 12 hours
6. Users receive update notifications

### Version Numbering

Follow Semantic Versioning: MAJOR.MINOR.PATCH

- 1.0.0 → 1.0.1 (Patch: Bug fixes)
- 1.0.0 → 1.1.0 (Minor: New features, backwards compatible)
- 1.0.0 → 2.0.0 (Major: Breaking changes)

### Testing Updates

To force check for updates:
```php
delete_site_transient( 'update_plugins' );
```

Or wait 12 hours for automatic check.

## Troubleshooting

**Updates not showing?**

1. Check version in code matches GitHub tag
2. Ensure release is published (not draft)
3. Verify repository is public
4. Check for PHP errors in debug.log
5. Force refresh: Delete `update_plugins` transient

**Update downloads but doesn't install?**

1. Check ZIP structure is correct:
   - Should be: `kr-toolkit/files`
   - Not: `kr-toolkit-main/files`

2. Check file permissions on server

**GitHub Rate Limit?**

GitHub limits anonymous API to 60 requests/hour per IP.
For high-traffic sites, add authentication token.

## Requirements

- PHP 7.4+
- WordPress 6.0+
- Plugin Update Checker library (included)
- Public GitHub repository

## Security

- Updates are served via HTTPS
- GitHub provides SSL certificates
- Plugin verifies source repository
- WordPress validates package integrity

## Support

Report issues: https://github.com/kerkeniaziz/kr-toolkit/issues