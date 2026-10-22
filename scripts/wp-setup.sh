#!/usr/bin/env bash
# Run from WordPress root. This script does not run automatically on deployment.
set -euo pipefail

if ! command -v wp >/dev/null 2>&1; then
  echo "WP-CLI is not installed. Install WordPress plugins through wp-admin." >&2
  exit 1
fi
if [[ ! -f wp-config.php ]]; then
  echo "Open the WordPress root directory containing wp-config.php before running this script." >&2
  exit 1
fi

echo "Checking WordPress plugin dependencies..."
if ! wp plugin is-installed seo-by-rank-math --quiet; then
  wp plugin install seo-by-rank-math
fi
wp plugin activate seo-by-rank-math

if wp plugin is-installed advanced-custom-fields-pro --quiet; then
  wp plugin activate advanced-custom-fields-pro
else
  echo "ACF Pro is NOT installed. Upload your licensed ACF Pro ZIP via Plugins -> Add New, then activate it." >&2
fi

if wp theme is-active bng --quiet; then
  wp eval 'bng_seed_sections();'
  wp rewrite flush
  echo "BNG section posts and rewrite rules updated."
else
  echo "BNG is not the active theme; activate it before seeding sections." >&2
fi
wp plugin list --fields=name,status,version
