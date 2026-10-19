# Fixsmith · PC Repair Studio (Laravel Application)

This project transforms the Figma PC Repair Website UI into a fully functional, live web application powered by **Laravel 13** and **PHP 8.5**, complete with **automated SMTP email notifications**, SQLite database persistence, and multi-role workspaces (**Customer**, **Technician**, and **Administrator**).

---

## 🚀 Quick Start (1-Click Run)

### Method 1: Batch Launcher
Double-click `start_website.bat` located inside this directory:
```bat
C:\Users\theer\Downloads\PC Repair Website UI\start_website.bat
```
This automatically starts the server and opens `http://127.0.0.1:8000` in your default browser.

### Method 2: Command Line
Open PowerShell or Command Prompt:
```bash
cd "C:\Users\theer\Downloads\PC Repair Website UI\laravel-app"
php artisan serve --port=8000
```
Then visit: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 📧 SMTP Email Notifications & Configuration

The application includes a built-in **SMTP Configuration & Testing Suite**:

1. Click the **"SMTP Email"** button in the top navigation bar.
2. Configure your SMTP provider:
   - **Mailtrap** (Sandbox testing): `smtp.mailtrap.io`, Port `587`, TLS
   - **Gmail**: `smtp.gmail.com`, Port `587`, TLS (Use an App Password)
   - **Sendgrid / Brevo / Custom SMTP**: Enter your host, port, username, password.
   - **Log Only mode**: Writes email contents directly to `storage/logs/laravel.log` (ideal for offline testing without internet).
3. Switch to the **"Send Test Email"** tab, enter any recipient address, and click **"Send Test Email Now"** to verify server connectivity.
4. Check the **"Email Activity Logs"** tab to view real-time audit logs of all sent emails.

### Automated Email Triggers:
- **New Repair Created**: Confirmation email to customer with delivery instructions (mail-in shipping or drop-off workshop location) + alert to Admin.
- **Status & Workshop Note Updated**: Real-time progress update to customer when technician alters repair status (e.g., *Diagnosing*, *Fixing*, *Waiting for part*, *Finished*).
- **Technician Assigned**: Alert to technician when a repair ticket is assigned to their active queue.
- **Rating or Complaint Submitted**: Alert to Admin when a customer leaves a review or files a complaint.
- **Complaint Response**: Email to customer when Admin responds to a complaint ticket.

---

## 👥 Pre-Configured Accounts & Live Role Switcher

You can switch roles instantly in the top bar (`Customer`, `Admin`, `Technician`) or log in with credentials:

| Role | Name | Email | Password |
|---|---|---|---|
| **Administrator** | Emma Walsh | `emma.walsh@fixsmith.com` | `password123` |
| **Manager** | Daniel Cho | `daniel.cho@fixsmith.com` | `password123` |
| **Senior Technician** | Alex Kim | `alex.kim@fixsmith.com` | `password123` |
| **Hardware Specialist** | Sam Rivera | `sam.rivera@fixsmith.com` | `password123` |
| **Repair Technician** | Priya Shah | `priya.shah@fixsmith.com` | `password123` |
| **Customer** | Maya Chen | `maya.chen@example.com` | `password123` |
| **Customer** | Jordan Blake | `jordan.blake@example.com` | `password123` |
| **Customer** | Noah Wilson | `noah.wilson@example.com` | `password123` |

---

## 🛠 Key Features

### 1. Customer Portal
- **Dashboard Overview**: Active repair tracking, auto-playing repair carousel with timeline milestones.
- **New Repair Ticket**: Serial number, symptom description, device photo upload, delivery selector (Mail-in vs Drop-off), and courier slip upload.
- **My Repairs**: Expandable accordion with real-time stage timeline (*Package received*, *Diagnosing*, *Fixing*, *Waiting for part*, *Finished*).
- **Rate & Complain**: 5-star rating submission or formal complaint submission with evidence upload.
- **Profile**: Edit contact phone and name.

### 2. Technician Workspace
- **Workspace Dashboard**: Unfinished repairs counter, waiting for parts, and completed statistics.
- **Assigned Queue**: Expandable repair cards with interactive 6-status selector and customer note editor. Clicking "Save update" persists to SQLite and sends an email to the customer.
- **Customer Feedback**: View ratings and complaints linked to repairs assigned to this technician.

### 3. Administrator Workshop Operations
- **Dashboard**: High-level workshop metrics, 7-day repair volume bar chart, and technician workload capacity meters.
- **Repair Requests Queue**: Live search, pagination, and instant technician reassignment dropdown.
- **Accounts Directory**: Add new users (Customer, Technician, Admin), edit names, reset passwords, and toggle active/deactivate accounts.
- **Team Workload Board**: Unassigned dispatch lane + technician lanes with capacity limits (e.g. 2/3). Draggable and reassignable untouched tickets.
- **Customer Feedback Center**: Unified inbox for ratings and complaints with reply editor that dispatches direct emails to customers.

### 4. Activity Center
- Slide-over notification drawer with unread counter, "Mark all as read" button, and direct navigation to tickets.

---

## 🧪 Testing

Run PHPUnit tests anytime:
```bash
cd "C:\Users\theer\Downloads\PC Repair Website UI\laravel-app"
php artisan test
```
All feature and unit tests (7 tests, 20 assertions) pass cleanly.
