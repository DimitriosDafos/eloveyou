# eLoveYou — Dating & Connection Platform

> A complete, production-ready dating platform built with Laravel 12. Designed to launch fast and monetize from day one.

---

## What is eLoveYou?

**eLoveYou** is a full-featured dating and connection web application. It includes everything you need to run a niche or general-purpose matchmaking platform — from user registration and profile browsing to real-time chat, photo moderation, and integrated payments.

Built for founders and developers who want a solid, maintainable codebase they can brand and ship quickly.

---

## Feature Overview

### For Users
- **Registration & Verification** — Email-verified sign-up, step-by-step profile setup
- **Profile & Photos** — Photo upload with admin approval, incognito mode, account deletion
- **Browse & Discover** — Filter by age group and interests/practices, paginated profiles
- **Matching** — Send, accept and decline match requests
- **Chat** — Unlock conversations via one-time payment or active subscription
- **Payments** — Chat unlock (€0.99 / €3.99), subscription plans — via **Stripe** and **PayPal**
- **Block & Report** — Full user safety toolset
- **Multi-Language** — English and German included, easily extendable

### For Operators
- **Admin Panel** — User management, photo moderation, reports overview
- **Subscription Management** — Track active subscribers and revenue
- **Message Filtering** — Built-in content filter service
- **GDPR-ready** — Terms of Service and Privacy Policy pages included

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 / PHP 8.4 |
| Database | MySQL / MariaDB |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Real-time | Laravel Reverb (WebSockets) |
| Payments | Stripe + PayPal |
| SMS/OTP | Twilio (optional) |
| Storage | Laravel Storage (local or S3-compatible) |

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Auth/              — Login, Registration, Verification
│   ├── Admin/             — Admin panel
│   ├── BrowseController   — Profile discovery & filters
│   ├── ChatController     — Chat & messaging
│   ├── MatchController    — Match requests
│   ├── PaymentController  — Stripe & PayPal integration
│   └── ProfileController  — Profile management & photos
├── Models/
│   ├── User, Photo, Chat, Message
│   ├── UserMatch, Block, Report
│   ├── Subscription, Payment, Practice
database/migrations/       — 10 clean migrations, fully structured
resources/views/           — Complete Blade view set (EN + DE)
```

---

## Monetization Model

eLoveYou ships with a **freemium + pay-per-action** model out of the box:

| Feature | Free | Subscriber (€/mo) |
|---------|------|-------------------|
| Browse profiles | ✓ | ✓ |
| Send match requests | ✓ | ✓ |
| Chat unlock | €3.99 / chat | €0.99 / chat |
| Subscription | — | Configurable |

Prices are configurable in the controller — no hardcoded values in views.

---

## Getting Started

```bash
git clone https://github.com/DimitriosDafos/eloveyou.git
cd eloveyou

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

Configure your `.env` with:
- Database credentials
- Stripe API keys (`STRIPE_KEY`, `STRIPE_SECRET`)
- PayPal credentials (`PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`)
- Mail driver (SMTP)
- Twilio (optional, for OTP SMS)
- Laravel Reverb (for real-time chat)

---

## Status

**Codebase complete.** All controllers, models, views, migrations and payment flows are implemented. The project requires environment configuration and final testing before going live.

This is a **startup-ready codebase** — built to be branded, configured, and launched.

---

## License & Purchase

This project is listed for acquisition. If you're interested in taking over development or purchasing the full rights, please get in touch via GitHub Issues or direct message.

**What's included in a purchase:**
- Full source code (this repository)
- Database schema & migrations
- All views (EN + DE)
- Payment integration (Stripe + PayPal)
- Admin panel
- 1 hour handover call (optional)

---

*Built with Laravel 12 · PHP 8.4 · Stripe · PayPal · Reverb*
