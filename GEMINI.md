# 🤖 GEMINI.md - Project Context: Lapas Jombang

This document serves as the **Source of Truth** for AI agents working on the **Lapas Kelas IIB Jombang - Sistem Layanan Kunjungan**. Use this context to understand the architecture, business logic, and coding standards.

---

## 🚀 1. Project Overview

**Lapas Jombang** is a web-based management system designed to digitalize the prison visit process. It handles visitor registration, inmate (WBP) data, queue management, and real-time reporting.

* **Primary Actors**:
  * **Guest (Pengunjung)**: Public users who register for visits.
  * **Admin/Petugas**: Verifies data, manages queues, scans QR codes.
  * **Superadmin (Kalapas)**: View executive dashboards and reports.

## 🛠 2. Tech Stack & Environment

* **Backend Framework**: Laravel 11/12 (PHP 8.2+)
* **Frontend**: Blade Templates + Alpine.js v3 + Tailwind CSS v3.
* **Build Tool**: Vite 5+.
* **Database**: MySQL 8.0 (InnoDB).
* **Queue Driver**: Redis (Preferred) or Database.
* **Real-time**: Laravel Reverb / Pusher (Broadcasting).
* **3D Graphics**: Three.js (Hero animation on landing page).

## 📂 3. Key Architecture & File Structure

### **Core Models & Tables**

* **`User`** (`users`): System administrators and staff. Roles: `admin`, `superadmin`, `petugas`.
* **`Wbp`** (`wbps`): Inmates data.
  * *Rel:* Has many `Kunjungan`.
* **`ProfilPengunjung`** (`profil_pengunjungs`): Master data for visitors (NIK, Name, Address).
  * *Rel:* Has many `Kunjungan`.
* **`Kunjungan`** (`kunjungans`): The central transaction table.
  * *Rel:* Belongs to `Wbp`, `ProfilPengunjung`.
  * *Key Fields:* `status`, `kode_booking`, `qr_token`, `sesi`, `tanggal_kunjungan`.
* **`Pengikut`** (`pengikuts`): Family members accompanying the main visitor.
* **`AntrianStatus`** (`antrian_status`): Real-time queue monitoring.

### **Important Services & Patterns**

* **`WhatsAppService`**: Handles API calls to Fonnte/Wablas. **MUST** be queued.
* **`ImageService`**: Compresses uploaded KTP/Photos using GD/Intervention.
* **`KunjunganObserver`**: Triggers notifications (WA/Email) upon status changes.
* **`KunjunganStatus` (Enum)**: Defines the lifecycle of a visit.

## 🔄 4. Business Logic & Workflows

### **Visit Lifecycle (Status Flow)**

1. **`PENDING`**: User submits form. Quota checked. Waiting for Admin verification.
2. **`APPROVED`**: Admin validates KTP & Relation. QR Code generated & sent via WA.
3. **`REJECTED`**: Data invalid. Reason sent to user.
4. **`ON_QUEUE`**: Visitor arrives at Lapas, scans QR at Gate.
5. **`CALLED`**: Queue number called by officer.
6. **`SERVING`**: Visitor is inside the meeting room.
7. **`COMPLETED`**: Visit finished. Satisfaction survey link sent.

### **Queue & Notifications**

* Heavy tasks (Image Compression, WhatsApp Sending, Email) **MUST** utilize `ShouldQueue`.
* Run `php artisan queue:work` is mandatory for the system to function correctly.

## 📝 5. Development Guidelines

### **Setup & Commands**

```bash
# Initial Setup
composer run setup  # (Custom script: install, key:gen, migrate, seed)

# Development Mode (Run all)
composer run dev

# Manual Workers
php artisan queue:listen --tries=3
php artisan schedule:work
Coding Standards
Strict Typing: Use PHP strict types where possible.

Fat Models, Skinny Controllers: Move complex logic to Services or Actions.

Blade Components: Use x-input-label, x-text-input for consistency.

Activity Logging: Ensure critical updates use activity()->log().

⚠️ 6. Common Pitfalls / Troubleshooting
Images 404: Always run php artisan storage:link after deployment.

WA Not Sending: Check if Queue Worker is running (supervisor or queue:work).

Vite Assets Missing: Ensure npm run build is executed on production.

Broadcasting Issues: Verify .env Reverb/Pusher credentials match the client side.
