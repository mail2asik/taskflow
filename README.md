# TaskFlow — Real-Time Project & Issue Management System

TaskFlow is a modern, real-time Kanban and project management web application built with a decoupled monorepo architecture. It features live WebSocket updates via Laravel Reverb, project-scoped issue tracking, automated activity observers, and a reactive Vue 3 frontend.

---

## 🚀 Key Features

* **Authentication & Authorization**: Secure token-based authentication using Laravel Sanctum.
* **Project Management**: Multi-project workspaces with member role assignments.
* **Project-Scoped Labels**: Custom labels and hex color tags scoped directly to individual projects.
* **Interactive Kanban Board**: Drag-and-drop issue status transitions with optimistic UI state updates.
* **Real-Time WebSockets**: Live cross-client board synchronization powered by Laravel Reverb and Redis.
* **Activity Audit Trail**: Automatic lifecycle tracking for issue creation, status changes, and assignments via Laravel Observers.
* **Comments & Attachments**: Rich issue discussions and file uploads powered by Laravel Storage.
* **Modern Executive Dashboard**: Clean layout featuring slide-over modals, dark sidebar navigation, and status badges built with Tailwind CSS.

---

## 🛠 Tech Stack

### Backend (`apps/api`)
* **Framework**: Laravel 11 (PHP 8.3)
* **API Engine**: REST API with Form Requests & API Resources
* **Database**: MySQL 8.0
* **Cache & Queue**: Redis
* **WebSockets**: Laravel Reverb
* **Testing**: Pest PHP

### Frontend (`apps/web`)
* **Framework**: Vue 3 (Composition API & Script Setup)
* **Language**: TypeScript
* **State Management**: Pinia
* **Router**: Vue Router 4
* **Styling**: Tailwind CSS
* **Build Tool**: Vite

---

## 📦 Project Structure

```text
.
├── apps/
│   ├── api/          # Laravel 11 REST API & WebSocket Backend
│   └── web/          # Vue 3 + TypeScript + Tailwind CSS Frontend
├── docker-compose.yml # Containerized services (App, MySQL, Redis)
└── README.md