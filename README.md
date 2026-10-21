# Laravel Reverb Real-time Chat

A lightweight, real-time public group chat application built with **Laravel**, **Laravel Reverb** (WebSockets), and **Tailwind CSS**. 

This application is designed for quick, anonymous, and ephemeral communication. It requires **no database**—all active users and messages are stored in Laravel's cache and automatically wiped clean every day at midnight.

Github cloned here: https://github.com/qirolab/laravel-reverb-chat.git

Demo production: https://real-time-chat-2j38.onrender.com/

---

## Features

* **Real-Time Messaging:** Powered by Laravel Reverb for instant WebSocket communication.
* **Database-Free Architecture:** Leverages Laravel's cache driver to store chat history and participant states entirely in memory or file cache.
* **Frictionless Authentication:** No passwords, emails, or registration required; users simply pick a username to join the conversation.
* **Ephemeral Data & Auto-Purge:** Messages and user sessions automatically expire at midnight every day.
* **Modern UI:** Styled cleanly and responsively with Tailwind CSS.

---

## Tech Stack

* **Backend:** Laravel 11
* **WebSockets:** Laravel Reverb
* **Frontend:** Blade, Tailwind CSS
* **Storage:** Laravel Cache (File / Redis)

---

## How It Works

* **Session Handling:** Authentication is purely session or cookie-based, mapping a chosen username to an active session without writing records to a persistent relational database.
* **Midnight Expiration:** The system calculates the exact seconds remaining until midnight (`now()->diffInSeconds(now()->tomorrow())`) and assigns that duration as the TTL (Time-To-Live) for cache entries. Once midnight arrives, the cache expires, instantly clearing all messages and user lists.
* **Broadcasting:** When a message is sent, it is pushed to Laravel Reverb via an event implementing `ShouldBroadcastNow`, broadcasting instantly to all connected clients in the open chat room.
