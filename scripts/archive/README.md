These helper scripts were moved here from `scripts/` for archival.

Files:
- `create_db.php` — creates the local `udkasemi` database (used for initial setup).
- `check_and_set_admin.php` — checks for a user with username/email `admin` and sets `role = 'admin'` if missing.
- `set_role_admin.php` — convenience script that sets the first `admin` user role to `admin`.

Usage (copy to top-level `scripts` or run directly):

PowerShell
```
php "scripts/archive/create_db.php"
php "scripts/archive/check_and_set_admin.php"
php "scripts/archive/set_role_admin.php"
```

These are kept for convenience/history. If you prefer permanent removal, delete this folder.