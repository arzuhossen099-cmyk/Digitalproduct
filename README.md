# Bangladesh Telecom & Rewards Web App

A complete PHP-based web application for telecom services and rewards, optimized for mobile devices and ready for cPanel deployment.

## Features
- **User System:** Registration, Login, Profile, Referral.
- **Wallet:** Automatic & Manual Deposit (bKash, Nagad, Rocket), Withdraw (bKash, Nagad), Transaction History.
- **SMS Automation:** Integrated Webhook for Android 'SMS Forwarder' apps to automate personal bKash/Nagad deposits.
- **Plan System:** Users must activate a plan (min 100 BDT) to access premium features.
- **Telecom Services:** Mobile Recharge, Internet/SMS/Talktime packages.
- **Rewards:** Daily Check-in, Ad-watching rewards.
- **Games:** Ludo and Spin Wheel (placeholders).
- **Notifications:** In-app notifications for transaction updates.
- **Admin Panel:** Comprehensive management of users, finances, plans, and site settings.

## Technical Stack
- PHP 8.2+
- MySQL
- Bootstrap 5
- FontAwesome 6
- jQuery

## Deployment Instructions (cPanel)
1. **Database Setup:**
   - Create a new MySQL database in cPanel.
   - Import `database.sql` into the database using phpMyAdmin.
2. **Configuration:**
   - Open `config/db.php`.
   - Update `$db`, `$user`, and `$pass` with your database credentials.
3. **Upload Files:**
   - Upload all project files to `public_html` or a subdirectory.
4. **Admin Access:**
   - Create an admin user by registering normally and then changing `is_admin` to `1` in the `users` table via phpMyAdmin.
   - Alternatively, use the following credentials if you ran a seed script:
     - **Username:** `admin`
     - **Password:** `admin123`
   - Admin panel is located at `/admin`.

## Security Notes
- Change the admin password immediately after first login.
- Ensure `config/db.php` is not publicly accessible (standard on cPanel).
- Use SSL (HTTPS) for secure transactions.
